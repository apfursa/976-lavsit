<?php

namespace app\controllers\productManagement;

use app\models\productManagement\HistoryProduct;
use app\models\productManagement\Master3;
use app\models\productManagement\Product;
use app\models\productManagement\WorkingHours;
use app\models\productManagement\Workplace;
use Bitrix24\Exceptions\Bitrix24ApiException;
use Bitrix24\Exceptions\Bitrix24EmptyResponseException;
use Bitrix24\Exceptions\Bitrix24Exception;
use Bitrix24\Exceptions\Bitrix24IoException;
use Bitrix24\Exceptions\Bitrix24MethodNotFoundException;
use Bitrix24\Exceptions\Bitrix24PaymentRequiredException;
use Bitrix24\Exceptions\Bitrix24PortalDeletedException;
use Bitrix24\Exceptions\Bitrix24PortalRenamedException;
use Bitrix24\Exceptions\Bitrix24SecurityException;
use Bitrix24\Exceptions\Bitrix24TokenIsExpiredException;
use Bitrix24\Exceptions\Bitrix24TokenIsInvalidException;
use Bitrix24\Exceptions\Bitrix24WrongClientException;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

// Рабочее место сотрудника

/**
 *
 */
class ProductController extends Controller
{
    /**
     * @return array<string, mixed>
     */
    public function behaviors():array
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
     * @return array<string, mixed>
     */
    public function actions():array
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
    public function actionLogout(): Response
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

    /**
     * @param int $product_id
     * @return string
     * @throws Bitrix24ApiException
     * @throws Bitrix24EmptyResponseException
     * @throws Bitrix24Exception
     * @throws Bitrix24IoException
     * @throws Bitrix24MethodNotFoundException
     * @throws Bitrix24PaymentRequiredException
     * @throws Bitrix24PortalDeletedException
     * @throws Bitrix24PortalRenamedException
     * @throws Bitrix24SecurityException
     * @throws Bitrix24TokenIsExpiredException
     * @throws Bitrix24TokenIsInvalidException
     * @throws Bitrix24WrongClientException
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function actionDone(int $product_id): string
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

    /**
     * @param int $product_id
     * @return string
     * @throws Bitrix24ApiException
     * @throws Bitrix24EmptyResponseException
     * @throws Bitrix24Exception
     * @throws Bitrix24IoException
     * @throws Bitrix24MethodNotFoundException
     * @throws Bitrix24PaymentRequiredException
     * @throws Bitrix24PortalDeletedException
     * @throws Bitrix24PortalRenamedException
     * @throws Bitrix24SecurityException
     * @throws Bitrix24TokenIsExpiredException
     * @throws Bitrix24TokenIsInvalidException
     * @throws Bitrix24WrongClientException
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function actionReturnProduct(int $product_id): string
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

    /**
     * @param int $product_id
     * @return string
     */
    public function actionStartTechnologicalPause(int $product_id): string
    {
//        $usersId = Yii::$app->user->id;
//        $master = Master3::getMasterByUserId($usersId);
        try {
            $product = Product::startTechnologicalPause($product_id);
        } catch (Bitrix24MethodNotFoundException|
        Bitrix24PaymentRequiredException|
        Bitrix24PortalDeletedException|
        Bitrix24PortalRenamedException|
        Bitrix24TokenIsExpiredException|
        Bitrix24TokenIsInvalidException|
        Bitrix24WrongClientException|
        Bitrix24ApiException|
        Bitrix24EmptyResponseException|
        Bitrix24IoException|
        Bitrix24SecurityException|
        Bitrix24Exception|
        \yii\db\Exception|Exception $e) {
        }
        return $this->render(
            'technologicalPause',
            [
                'product_name' => $product->title,
                'product_id' => $product->id,
            ]
        );
    }

    /**
     * @param int $product_id
     * @return string
     */
    public function actionEndTechnologicalPause(int $product_id): string
    {
        $product = Product::endTechnologicalPause($product_id);
        $deadline = date('d-m-Y', strtotime($product->deadline));
        return $this->render(
            'main',
            [
                'product_name' => $product->title,
                'product_id' => $product->id,
                'link' => $product->link,
                'deadline' => $deadline
            //                    'master_id' => $product->masterId
            ]
        );
//        Yii::$app->response->redirect('https://test.mysmartautomation.ru/productManagement/workplace/start');
    }

    /**
     * @param $product_id
     * @return string
     */
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

    /**
     * @param $product_id
     * @return string
     */
    public function actionEndPause($product_id)
    {
        $product = Product::endPause($product_id);
        $deadline = date('d-m-Y', strtotime($product->deadline));
        return $this->render(
            'main',
            [
                'product_name' => $product->title,
                'product_id' => $product->id,
                'link' => $product->link,
                'deadline' => $deadline
            //                    'master_id' => $product->masterId
            ]
        );
//        Yii::$app->response->redirect('https://test.mysmartautomation.ru/productManagement/workplace/render-main');
    }
}
