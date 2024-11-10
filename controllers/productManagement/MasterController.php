<?php

namespace app\controllers\productManagement;

use app\models\productManagement\Master;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

/**
 *
 */
class MasterController extends Controller
{
    /**
     * @return mixed[]
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'logout',
                    'start',
                    'end-working-day',
                    'return-product',
                    'technological-pause-start',
                    'technological-pause-end',
                    'done',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'logout',
                            'start',
                            'end-working-day',
                            'return-product',
                            'technological-pause-start',
                            'technological-pause-end',
                            'done',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['login', 'signup'],
                        'roles' => ['?'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null, // @phpstan-ignore-line
            ],
        ];
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * @return string|void
     */
    public function actionIndex()
    {
        $usersId = Yii::$app->user->id;
        $master = $this->getMasterByUserId($usersId);
        $model = new Master();
        $arrData = $model->start();
        if ($arrData['page'] == 1) {
            return $this->render('start1');
        }
        if ($arrData['page'] == 2) {
            return $this->render(
                'start2',
                [
                    'product_name' => $arrData['product_name'],
                    'product_id' => $arrData['product_id'],
                    'master_id' => $arrData['master_id'],
                ]
            );
        }
        if ($arrData['page'] == 'technologicalPause') {
            return $this->render(
                'technologicalPause',
                [
                    'product_name' => $arrData['product_name'],
                    'product_id' => $arrData['product_id'],
                    'master_id' => $arrData['master_id'],
                ]
            );
        }
        if ($arrData['page'] == 3) {
            return $this->render('start3');
        }
        if ($arrData['page'] == 'continue') {
            return $this->render(
                'continue',
                [
                    'master_id' => $arrData['master_id'],
                ]
            );
        }
        if ($arrData['page'] == 9) {
            return $this->render(
                'page9',
                [
                    'master_id' => $arrData['master_id'],
                ]
            );
        }
        if ($arrData['page'] == 10) {
            return $this->render('page10');
        }
    }

    /**
     * @param $product_id
     * @param $master_id
     * @return Response
     */
    public function actionEndWorkingDay($product_id = null, $master_id = null)
    {
        $model = new Master();
        $model->endWorkingDay($product_id, $master_id);
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * @param $product_id
     * @return string
     */
    public function actionReturnProduct($product_id)
    {
        $model = new Master();
        $arrData = $model->returnProduct($product_id);
        return $this->render(
            'start1',
            [
                'master_id' => $arrData['master_id'],
            ]
        );
//        if($arrData['page'] == 4){
//            return $this->render('start3');
//        }
//        if($arrData['page'] == 7){
//            return $this->render('start7');
//        }
//        Yii::$app->user->logout();
//        return $this->goHome();
    }

    /**
     * @param $product_id
     * @return string
     */
    public function actionTechnologicalPauseStart($product_id)
    {
        $model = new Master();
        $arrData = $model->technologicalPauseStart($product_id);
        return $this->render(
            'technologicalPause',
            [
                'product_name' => $arrData['product_name'],
                'product_id' => $arrData['product_id'],
            ]
        );
    }

    /**
     * @param $product_id
     * @return string
     */
    public function actionTechnologicalPauseEnd($product_id)
    {
        $model = new Master();
        $arrData = $model->technologicalPauseEnd($product_id);
        return $this->render(
            'start2',
            [
                'product_name' => $arrData['product_name'],
                'product_id' => $arrData['product_id'],
            ]
        );
    }

    /**
     * @param $product_id
     * @return string
     */
    public function actionDone($product_id)
    {
        $model = new Master();
        $arrData = $model->done($product_id);
        return $this->render(
            'start1',
            [
                'master_id' => $arrData['master_id'],
            ]
        );
    }

    /**
     * @param $usersId
     * @return mixed
     * @throws \Bitrix24\Exceptions\Bitrix24ApiException
     * @throws \Bitrix24\Exceptions\Bitrix24EmptyResponseException
     * @throws \Bitrix24\Exceptions\Bitrix24Exception
     * @throws \Bitrix24\Exceptions\Bitrix24IoException
     * @throws \Bitrix24\Exceptions\Bitrix24MethodNotFoundException
     * @throws \Bitrix24\Exceptions\Bitrix24PaymentRequiredException
     * @throws \Bitrix24\Exceptions\Bitrix24PortalDeletedException
     * @throws \Bitrix24\Exceptions\Bitrix24PortalRenamedException
     * @throws \Bitrix24\Exceptions\Bitrix24SecurityException
     * @throws \Bitrix24\Exceptions\Bitrix24TokenIsExpiredException
     * @throws \Bitrix24\Exceptions\Bitrix24TokenIsInvalidException
     * @throws \Bitrix24\Exceptions\Bitrix24WrongClientException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function getMasterByUserId($usersId)
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => 1036, // СП Мастер
                'filter' => ['ufCrm14Userid' => $usersId]
            ]
        );
        $master = $answerB24['result']['items'][0];
        return $master;
    }
}
