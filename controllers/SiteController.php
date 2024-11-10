<?php

namespace app\controllers;

use app\models\productManagement\Master;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * @return mixed[]
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout','start','start2'],
                'rules' => [
                    [
                        'actions' => [
                            'logout',
//                            'start',
                            'start2'
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (
            \Yii::$app->getUser()->isGuest
        ) {
            \Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl);
        } else {
            Yii::$app->response->redirect(Url::to('productManagement/workplace/start'));
        }
        return $this->render('index');
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
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /*
    public function actionStart($continue = false)
    {
        $model = new Master();
        $arrData = $model->start($continue);
        if($arrData['page'] == 1){
            return $this->render('start1');
        }
        if($arrData['page'] == 2){
            return $this->render(
                'start2',
                [
                    'product_name' => $arrData['product_name'],
                    'product_id' => $arrData['product_id']
                ]
            );
        }
        if($arrData['page'] == 3){
            return $this->render('start3');
        }
        if($arrData['page'] == 'continue'){
            return $this->render('continue');
        }
        if($arrData['page'] == 9){
            return $this->render('page9');
        }
        if($arrData['page'] == 10){
            return $this->render('page10');
        }
    }
    */

    /**
     * @return string
     */
    public function actionStart2()
    {
        return $this->render('start2');
    }
}
