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

    public function actionStart($continue = false)
    {
        $model = new Master();
        $arrData = $model->start($continue);
        Yii::warning($arrData, 'MasterController_Start_$arrData_112');
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
            return $this->render('continue',
                [
                    'master_id' => $arrData['master_id'],
                ]
            );
        }
        if ($arrData['page'] == 9) {
            return $this->render('page9',
                [
                    'master_id' => $arrData['master_id'],
                ]
            );
        }
        if ($arrData['page'] == 10) {
            return $this->render('page10');
        }
    }

    public function actionEndWorkingDay($product_id = null, $master_id = null)
    {
        Yii::warning($product_id, 'actionEndWorkingDay_$product_id');
        Yii::warning($master_id, 'actionEndWorkingDay_$master_id');
        $model = new Master();
        $model->endWorkingDay($product_id, $master_id);
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionReturnProduct($product_id)
    {
        $model = new Master();
        $arrData = $model->returnProduct($product_id);
        return $this->render('start1',
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

    public function actionTechnologicalPauseStart($product_id)
    {
        $model = new Master();
        $arrData = $model->technologicalPauseStart($product_id);
        return $this->render('technologicalPause',
            [
                'product_name' => $arrData['product_name'],
                'product_id' => $arrData['product_id'],
            ]
        );
    }

    public function actionTechnologicalPauseEnd($product_id)
    {
        $model = new Master();
        $arrData = $model->technologicalPauseEnd($product_id);
        return $this->render('start2',
            [
                'product_name' => $arrData['product_name'],
                'product_id' => $arrData['product_id'],
            ]
        );
    }

    public function actionDone($product_id)
    {
        $model = new Master();
        $arrData = $model->done($product_id);
        return $this->render('start1',
            [
                'master_id' => $arrData['master_id'],
            ]
        );
    }
}
