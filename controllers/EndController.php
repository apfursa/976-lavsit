<?php

namespace app\controllers;

use app\models\productManagement\Master;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class EndController extends Controller
{
    /**
     * @return mixed[]
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['end-working-day'],
                'rules' => [
                    [
                        'actions' => ['end-working-day'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
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

    public function actionEndWorkingDay()
    {
        $model = new Master();
        $model->end();
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
