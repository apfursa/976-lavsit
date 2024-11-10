<?php

namespace app\controllers\productManagement;

use app\models\productManagement\HistoryProduct;
use app\models\productManagement\Master;
use app\models\productManagement\Product;
use app\models\productManagement\WorkingHours;
use app\models\productManagement\Workplace;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

// Рабочее время

/**
 *
 */
class WorkingHoursController extends Controller
{
    /**
     * @var bool
     */
    public $enableCsrfValidation = false;

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
     * @param $continue
     * @return string|void
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
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\db\Exception
     */
    public function actionStart($continue = false)
    {
        $usersId = Yii::$app->user->id;
        $master = $this->getMasterByUserId($usersId);
        $typeOfCompletion = WorkingHours::howWorkingDayCompleted($master);
        if ($typeOfCompletion == 'completed automatically') {
            Yii::$app->response->redirect(Url::to('productManagement/master/start'));
        }
        if (
            $typeOfCompletion == 'no entry' ||
            $typeOfCompletion == 'completed by the user' ||
            $typeOfCompletion == 'fixed by user'
        ) {
            WorkingHours::add($master);
//            if(){
//                WorkingHours::add($master);
//            }else{
//                return $this->render(
//                    'main',
//                    [
//                        'product_name' => $arrData['product_name'],
//                        'product_id' => $arrData['product_id'],
//                        'master_id' => $arrData['master_id'],
//                    ]
//                );
//            }
        }


        $model = new Master();
        $arrData = $model->start($continue);
        if ($arrData['page'] == 1) {
            return $this->render('start1');
        }
        if ($arrData['page'] == 'main') {
            return $this->render(
                'main',
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

    /**
     * @return string|void
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
    public function actionChangeEndTimeOfWorkingDay()
    {
        $usersId = Yii::$app->user->id;
        $master = $this->getMasterByUserId($usersId);
        $filter = [
            'ufCrm18Master' => $master['id'],
            'ufCrm18TerminatioType' => 892 // Завершено автоматически
        ];
        $order = [
            'id' => 'DESC', // сортировать по убыванию
        ];
        $models = WorkingHours::list($filter, $order);
        if ($models != []) {
            $model = $models[0];
            return $this->render(
                'changeEndTimeOfWorkingDay',
                [
                    'master_id' => $master['id'],
                    'timeEnd' => date("H:i:s", strtotime($model->timeEnd)),
                    'dateEnd' => date("Y-m-d", strtotime($model->dateEnd)),
                    'entity_id' => $model->id,
                ]
            );
        }
    }

    /**
     * @param $product_id
     * @param $master_id
     * @return Response
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
    public function actionEndWorkingDay($product_id = null, $master_id = null)
    {
//        Yii::warning($product_id, 'actionEndWorkingDay_$product_id');
//        Yii::warning($master_id, 'actionEndWorkingDay_$master_id');
        WorkingHours::endWorkingDay($product_id, $master_id);
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * @return void
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
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\db\Exception
     */
    public function actionChangeTime()
    {
        $request = Yii::$app->request;
        $date = $request->post('date');
        Yii::warning($date, '$date');
        $time = $request->post('time');
        Yii::warning($time, '$time');
        $entity_id = $request->post('entity_id');
        $master_id = $request->post('master_id');
        $fields = [
            'ufCrm18UshelTime' => $time,
            'ufCrm18UshelData' => $date,
            'ufCrm18TerminatioType' => 894, // Исправлено пользователем
        ];
        WorkingHours::update($entity_id, $fields);

        $filterProduct = [
            'ufCrm8Master' => $master_id,
            'ufCrm8Status' => 898, // в работе
        ];
        $products = Product::list($filterProduct);
        if ($products == []) {
            $filterProduct = [
                'ufCrm8Master' => $master_id,
                'ufCrm8Status' => 902, // технологическая пауза
            ];
            $products = Product::list($filterProduct);
        }
        if ($products == []) {
            $filterProduct = [
                'ufCrm8Master' => $master_id,
                'ufCrm8Status' => 904, // пауза
            ];
            $products = Product::list($filterProduct);
        }
//        ArrayHelper::toArray($products, '$products');
        if ($products != []) {
            $component = new \wm\b24tools\b24Tools();
            $b24App = $component->connectFromAdmin();
            $obB24 = new \Bitrix24\B24Object($b24App);
            $request = $obB24->client->call(
                'crm.item.list',
                [
                    'entityTypeId' => 1042, // История изделия
                    'order' => ['id' => 'DESC'], // по убыванию
                    'filter' => [
                        'ufCrm16Master' => $master_id,
                    ]
                ]
            )['result']['items'];
            if ($request != []) {
                $history = $request[0];
//                ArrayHelper::toArray($history, '$history');
                $fieldsHistory = [
                    'ufCrm16Master' => $master_id,
                    'ufCrm16Status' => $history['ufCrm16Status'],
                    'ufCrm16Srochnost' => $history['ufCrm16Srochnost'],
                    'parentId187' => $history['parentId187'],
                    'ufCrm16Operation' => 908, // конец рабочего дня
                    'ufCrm16Uchastok' => $history['ufCrm16Uchastok'], // участок
                    'ufCrm16Dateandtime' => date('d-m-Y ' . $time, strtotime($date)), // дата и время
                ];
                HistoryProduct::add($fieldsHistory);
            }
        }

        sleep(10);
        Yii::$app->response->redirect('https://sof.lavsit.ru/productManagement/workplace/start');
    }
}
