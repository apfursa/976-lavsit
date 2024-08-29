<?php

namespace app\controllers\productManagement;

use app\models\productManagement\HistoryProduct;
use app\models\productManagement\Master3;
use app\models\productManagement\Product;
use app\models\productManagement\WorkingHours;
use app\models\productManagement\Workplace;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

// Рабочее место сотрудника
class ProductController extends Controller
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
//                    'start',
//                    'end-working-day',
                    'return-product',
                    'start-technological-pause',
                    'end-technological-pause',
                    'done',
                    'start-pause',
                    'end-pause',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'logout',
//                            'start',
//                            'end-working-day',
                            'return-product',
                            'start-technological-pause',
                            'end-technological-pause',
                            'done',
                            'start-pause',
                            'end-pause',
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


    /*
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
    */

    public function actionDone($product_id)
    {
        $usersId = Yii::$app->user->id;
        $master = Master3::getMasterByUserId($usersId);
        Product::done($product_id);
        return $this->render(
            'takeProduct',
            [
                'master_id' => $master['id'],
            ]
        );
    }

    public function actionReturnProduct($product_id)
    {
        $usersId = Yii::$app->user->id;
        $master = Master3::getMasterByUserId($usersId);
        Product::returnProduct($product_id);
        return $this->render(
            'takeProduct',
            [
                'master_id' => $master['id'],
            ]
        );
    }

    public function actionStartTechnologicalPause($product_id)
    {
//        $usersId = Yii::$app->user->id;
//        $master = Master3::getMasterByUserId($usersId);
        $product = Product::startTechnologicalPause($product_id);
        return $this->render(
            'technologicalPause',
            [
                'product_name' => $product->title,
                'product_id' => $product->id,
            ]
        );
    }

    public function actionEndTechnologicalPause($product_id)
    {
        $product = Product::endTechnologicalPause($product_id);
        return $this->render(
            'main',
            [
                'product_name' => $product->title,
                'product_id' => $product->id,
                'link' => $product->link
//                    'master_id' => $product->masterId
            ]
        );
//        Yii::$app->response->redirect('https://test.mysmartautomation.ru/productManagement/workplace/start');
    }

    public function actionStartPause($product_id)
    {
//        $usersId = Yii::$app->user->id;
//        $master = Master3::getMasterByUserId($usersId);
        $product = Product::startPause($product_id);
        return $this->render(
            'pause',
            [
                'product_name' => $product->title,
                'product_id' => $product->id,
            ]
        );
    }

    public function actionEndPause($product_id)
    {
        $product = Product::endPause($product_id);
        return $this->render(
            'main',
            [
                'product_name' => $product->title,
                'product_id' => $product->id,
                'link' => $product->link
//                    'master_id' => $product->masterId
            ]
        );
//        Yii::$app->response->redirect('https://test.mysmartautomation.ru/productManagement/workplace/render-main');
    }

}
