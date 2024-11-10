<?php

namespace app\controllers\handlers\placements;

use wm\yii\filters\auth\HttpBearerAuth;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use Bitrix24\User\User as B24User;
use wm\admin\models\User;

/**
 *
 */
class PlacementBaseController extends Controller
{
    /**
     * @var null
     */
    protected $accessToken = null;

    /**
     * @var string
     */
    public $layout = '@app/views/layouts/app.php';

//    public function behaviors() {
//        $behaviors = parent::behaviors();
//        return [
//            'authenticator' => [
//                'class' => CompositeAuth::class(),
//                'authMethods' => [
//                    HttpBearerAuth::class(),
//                ],
//            ],
//        ];
//    }

    /**
     * @param $action
     * @return bool
     * @throws HttpException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        $request = Yii::$app->request;
        $session = Yii::$app->session;

        if (
            $request->get('DOMAIN')
            && $request->post('member_id')
            && $request->post('AUTH_ID')
            && $request->post('REFRESH_ID')
        ) {
            $component24 = new \wm\b24tools\b24Tools();
            $arAccessParams = $component24->prepareFromRequest(Yii::$app->request->post(), Yii::$app->request->get());
            $errors = $component24->checkB24Auth();
            if ($errors) {
                throw new HttpException(403, 'В доступе отказано');
            }
            $b24App = $component24->connectFromUser($arAccessParams);
            $obB24 = new B24User($b24App);
            $b24User = $obB24->current()['result'];
            $user = User::findByBitrixId(ArrayHelper::getValue($b24User, 'ID'));
            if (!$user) {
                $userPassword = User::generatePassword();
                $user = new User();
                $user->username = ArrayHelper::getValue($b24User, 'EMAIL');
                $user->b24_user_id = ArrayHelper::getValue($b24User, 'ID');

                $user->name = ArrayHelper::getValue($b24User, 'NAME');
                $user->last_name = ArrayHelper::getValue($b24User, 'LAST_NAME');

                $user->password = \Yii::$app->security->generatePasswordHash($userPassword);
                $user->getAccessToken();
                $user->save();
            } else {
                $user->getAccessToken();
                $user->save();
            }
            $this->accessToken = $user->access_token;

            Yii::$app->user->login($user, 3600 * 24 * 30);

            $session->set('accessAllowed', true);
            $session['AccessParams'] = $arAccessParams;
        }


        if (
            null === $request->get('DOMAIN')
            or null === $request->post('member_id')
            or null === $request->post('AUTH_ID')
            or null === $request->post('REFRESH_ID')
        ) {
            throw new HttpException(404, 'Приложение необходимо запустить из портала Битрикс24');
        }
        return parent::beforeAction($action);
    }
}
