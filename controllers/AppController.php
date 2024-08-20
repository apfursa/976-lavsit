<?php

namespace app\controllers;

use app\models\App;
use Bitrix24\User\User as B24User;
use wm\admin\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;

/**
 * Class AppController
 * @package app\controllers
 *
 * @property string $portalTableName
 */
class AppController extends Controller
{
    /**
     * @var null
     */
    protected $accessToken = null;

    /**
     * @var string
     */
    public $layout = '@app/views/layouts/app.php';

    /**
     * @param \yii\base\Action $action
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
            if(!$b24App){
                throw new HttpException(403, 'В доступе отказано');
            }
            $obB24 = new B24User($b24App);
            $b24User = $obB24->current()['result'];
            $user = User::findByBitrixId(ArrayHelper::getValue($b24User, 'ID'));
            if (!$user) {
                //Yii::warning('$user1', '$user1');
                $userPassword = User::generatePassword();
                $user = new User();
                $user->username = strval(ArrayHelper::getValue($b24User, 'EMAIL'));
                $user->b24_user_id = intval(ArrayHelper::getValue($b24User, 'ID'));

                $user->name = ArrayHelper::getValue($b24User, 'NAME');
                $user->last_name = ArrayHelper::getValue($b24User, 'LAST_NAME');

                $user->password = \Yii::$app->security->generatePasswordHash($userPassword);
                $user->getAccessToken();
                $user->b24AccessParams = json_encode($arAccessParams)?:'';
                $user->save();
                Yii::warning($user->errors, '$user->errors1');
            } else {
                //Yii::warning('$user2', '$user2');
                $user->getAccessToken();
                $user->b24AccessParams = json_encode($arAccessParams)?:'';
                //$user->generateAccessTokenTest();
                $user->save();
                //Yii::warning($user->errors, '$user->errors');
            }
            $this->accessToken = $user->access_token;
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

    /**
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionInstall()
    {
        $component24 = new \wm\b24tools\b24Tools();
        $request = Yii::$app->request;
        $arAccessParams = $component24->prepareFromRequest(Yii::$app->request->post(), Yii::$app->request->get());
        $result = $component24->addAuthToDB($this->portalTableName, $arAccessParams);
        Yii::$app->db
            ->createCommand()
            ->update(
                'admin_menu_item',
                [
                    'params' => '{"url":"' . Yii::$app->request->hostInfo . '/admin' . '"}'
                ],
                'id = 2'
            )
            ->execute();
        App::install();
        if ($result == 1) {
            return $this->render('install');
        }
        return 'Ошибка записи';
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $placementOptions = json_decode($request->post('PLACEMENT_OPTIONS'));
        $placement = $request->post('PLACEMENT');
        $this->routing($placementOptions);

        $params = [
            'placement' => $placement,
            'placementOptions' => $placementOptions,
        ];
        $userId = Yii::$app->user->id;
        $portalName = User::getPortalName($userId);

        $appUrl = 'https://' . $portalName . '/marketplace/app/' . 1 . '/';
        //TODO B24ConnectSettings::getParametrByName('appId')

        return $this->render(
            'index',
            ['params' => json_encode($params), 'accessToken' => $this->accessToken, 'appUrl' => $appUrl]
        );
    }


    /**
     * @param mixed $param
     * @return bool|void
     * @throws \Exception
     */
    protected function routing($param)
    {
        $tempParam = [];
        $tempParam['route'] = $this->getType(ArrayHelper::getValue($param, 'route'));
        $tempParam['handler'] = (ArrayHelper::getValue($param, 'handler')) ?
            $this->getUrl(ArrayHelper::getValue($param, 'handler')) :
            $this->getUrl(ArrayHelper::getValue($param, 'url'));
        if (!$tempParam['handler']) {
            return false;
        }

        if ($tempParam['route'] == 'portal') {
            $simbol = (strpos($tempParam['handler'], '?') === false) ? '?' : '&';
            header('Location: ' . $tempParam['handler'] . $simbol . 'IFRAME=Y&IFRAME_TYPE=SIDE_SLIDER');
            die;
        }
    }

    /**
     * @param string $param
     * @return string
     */
    protected function getType($param)
    {

        $baseTypies = ['app', 'portal'];
        if (!$param) {
            return 'app';
        }

        if (in_array($param, $baseTypies)) {
            return $param;
        } else {
            return 'app';
        }
    }

    /**
     * @param string $param
     * @return bool|string
     */
    protected function getUrl($param)
    {
        if ($param) {
            return $param;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getPortalTableName()
    {
        return 'admin_b24portal';
    }
}
