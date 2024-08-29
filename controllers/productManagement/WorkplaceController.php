<?php

namespace app\controllers\productManagement;

use app\models\productManagement\HistoryProduct;
use app\models\productManagement\Master3;
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

// Рабочее место сотрудника
class WorkplaceController extends Controller
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
                ],
                'rules' => [
                    [
                        'actions' => [
                            'logout',
                            'start',
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

    public function actionStart()
    {
        $usersId = Yii::$app->user->id;
        $master = Master3::getMasterByUserId($usersId);
        $typeOfCompletion = WorkingHours::howWorkingDayCompleted($master);
        if ($typeOfCompletion == 'completed automatically') {
            Yii::$app->response->redirect('https://test.mysmartautomation.ru/productManagement/working-hours/change-end-time-of-working-day');
        }
        if ($typeOfCompletion == 'no entry' || $typeOfCompletion == 'completed by the user' || $typeOfCompletion == 'fixed by user') {
            WorkingHours::add($master);
            $orderHistoryProduct = ['id' => 'DESC']; // В порядке убывания
            $filterHistoryProduct = [
                'ufCrm16Master' => $master['id'],
            ];
            $historys = HistoryProduct::list($filterHistoryProduct, $orderHistoryProduct);
            if ($historys != []) {
                if ($historys[1]) {
                    $history = $historys[1]; // предпоследняя запись
                    // поставил на технологическую паузу
                    if ($history->operationId == 914) {
                        $product = Product::get($history->productId);
                        $historyProductUchastok = (new \yii\db\Query())
                            ->select(['historyProductUchastok'])
                            ->from('workshop')
                            ->where(['masterWorkshop' => $master['ufCrm14Workshop']])
                            ->one()['historyProductUchastok'];
                        $historyFields = [
                            'ufCrm16Master' => $master['id'],
                            'ufCrm16Status' => 886, // технологическая пауза
                            'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
                            'parentId187' => $product->id,
                            'ufCrm16Operation' => 906, // начало рабочего дня
                            'ufCrm16Uchastok' => $historyProductUchastok // участок
                        ];
                        HistoryProduct::add($historyFields);
                        return $this->render(
                            'technologicalPause',
                            [
                                'product_name' => $product->title,
                                'product_id' => $product->id,
                            ]
                        );
                    }
                }

            }
            $products = Product::getProductsWithStatusInWork($master);
            if (count($products) > 1) {
                return $this->render(
                    'conflict',
                    [
                        'master_id' => $master['id'],
                    ]
                );
            }
            if (count($products) == 1) {
                $product = $products[0];
                $historyProductUchastok = (new \yii\db\Query())
                    ->select(['historyProductUchastok'])
                    ->from('workshop')
                    ->where(['masterWorkshop' => $master['ufCrm14Workshop']])
                    ->one()['historyProductUchastok'];
                $historyFields = [
                    'ufCrm16Master' => $master['id'],
                    'ufCrm16Status' => 882, // в работе
                    'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
                    'parentId187' => $product->id,
                    'ufCrm16Operation' => 906, // начало рабочего дня
                    'ufCrm16Uchastok' => $historyProductUchastok // участок
                ];
                HistoryProduct::add($historyFields);
//                $latestProductHistory = HistoryProduct::getLatestProductHistory($product);
                return $this->render(
                    'main',
                    [
                        'product_name' => $product->title,
                        'product_id' => $product->id,
                        'link' => $product->link
//                    'master_id' => $master['id'],
                    ]
                );
            }
            $productOrder = [
                'ufCrm8_1701952418411' => 'ASC', // сортировать по возрастанию
            ];
            if ($products == []) {
                $productFilter = [
                    'ufCrm8_1705585967731' => 766, // Срочность ***
                    'ufCrm8Master' => $master['id'],
                    'ufCrm8Status' => 900, // в очереди
                ];
                $products = Product::list($productFilter, $productOrder);
            }
            if ($products == []) {
                $productFilter = [
                    'ufCrm8_1705585967731' => 764, // Срочность **
                    'ufCrm8Master' => $master['id'],
                    'ufCrm8Status' => 900, // в очереди
                ];
                $products = Product::list($productFilter, $productOrder);
            }
            if ($products == []) {
                $productFilter = [
                    'ufCrm8_1705585967731' => 754, // Срочность *
                    'ufCrm8Master' => $master['id'],
                    'ufCrm8Status' => 900, // в очереди
                ];
                $products = Product::list($productFilter, $productOrder);
            }
            $stageId = (new \yii\db\Query())
                ->select(['stageId'])
                ->from('workshop')
                ->where(['masterWorkshop' => $master['ufCrm14Workshop']])
                ->one()['stageId'];
            if ($products == []) {
                $productFilter = [
                    'ufCrm8_1705585967731' => 766, // Срочность ***
                    'ufCrm8Status' => 896, // на складе
                    'stageId' => $stageId, // стадия/участок
                ];
                $products = Product::list($productFilter, $productOrder);
            }
            if ($products == []) {
                $productFilter = [
                    'ufCrm8_1705585967731' => 764, // Срочность **
                    'ufCrm8Status' => 896, // на складе
                    'stageId' => $stageId, // стадия/участок
                ];
                $products = Product::list($productFilter, $productOrder);
            }
            if ($products == []) {
                $productFilter = [
                    'ufCrm8_1705585967731' => 754, // Срочность *
                    'ufCrm8Status' => 896, // на складе
                    'stageId' => $stageId, // стадия/участок
                ];
                $products = Product::list($productFilter, $productOrder);
            }
            if (count($products) == 0) {
                return $this->render(
                    'noProduct',
                    [
                        'master_id' => $master['id']
                    ]
                );
            }
            if (count($products) >= 1) {
                $product = $products[0];
                $productFields = [
                    'ufCrm8Status' => 898, // в работе
                    'ufCrm8Master' => $master['id'],
                ];
                Product::update($product->id, $productFields);

                $historyProductUchastok = (new \yii\db\Query())
                    ->select(['historyProductUchastok'])
                    ->from('workshop')
                    ->where(['masterWorkshop' => $master['ufCrm14Workshop']])
                    ->one()['historyProductUchastok'];
                $historyFields = [
                    'ufCrm16Master' => $master['id'],
                    'ufCrm16Status' => 882, // в работе
                    'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
                    'parentId187' => $product->id,
                    'ufCrm16Operation' => 906, // начало рабочего дня
                    'ufCrm16Uchastok' => $historyProductUchastok // участок
                ];
                HistoryProduct::add($historyFields);

                return $this->render(
                    'main',
                    [
                        'product_name' => $product->title,
                        'product_id' => $product->id,
                        'link' => $product->link
//                    'master_id' => $product->masterId
                    ]
                );
            }
        }

        $orderHistoryProduct = ['id' => 'DESC']; // В порядке убывания
        $filterHistoryProduct = [
            'ufCrm16Master' => $master['id'],
        ];
        $historys = HistoryProduct::list($filterHistoryProduct, $orderHistoryProduct);
        if ($historys != []) {
            $history = $historys[0];
            // начало рабочего дня
            if ($history->operationId == 906) {
                $product = Product::get($history->productId);
                // технологическая пауза
                if ($history->statusId == 886) {
                    return $this->render(
                        'technologicalPause',
                        [
                            'product_name' => $product->title,
                            'product_id' => $product->id,
                        ]
                    );
                }
                // в работе
                if ($history->statusId == 882) {
                    return $this->render(
                        'main',
                        [
                            'product_name' => $product->title,
                            'product_id' => $product->id,
                            'link' => $product->link
                        ]
                    );
                }

            }
            // поставил на технологическую паузу
            if ($history->operationId == 914) {
                $product = Product::get($history->productId);
                // технологическая пауза
                if ($history->statusId == 886) {
                    return $this->render(
                        'technologicalPause',
                        [
                            'product_name' => $product->title,
                            'product_id' => $product->id,
                        ]
                    );
                }
            }
            // снял с технологической паузы
            if ($history->operationId == 916) {
                $product = Product::get($history->productId);
                return $this->render(
                    'main',
                    [
                        'product_name' => $product->title,
                        'product_id' => $product->id,
                        'link' => $product->link
                    ]
                );
            }
            // поставил на паузу
            if ($history->operationId == 910) {
                $product = Product::get($history->productId);
                return $this->render(
                    'pause',
                    [
                        'product_name' => $product->title,
                        'product_id' => $product->id,
                    ]
                );
            }
            // снял с паузы
            if ($history->operationId == 912) {
                $product = Product::get($history->productId);
                return $this->render(
                    'main',
                    [
                        'product_name' => $product->title,
                        'product_id' => $product->id,
                        'link' => $product->link
                    ]
                );
            }
            // взял в работу
            if ($history->operationId == 922) {
                $product = Product::get($history->productId);
                return $this->render(
                    'main',
                    [
                        'product_name' => $product->title,
                        'product_id' => $product->id,
                        'link' => $product->link
                    ]
                );
            }
        }
        $products = Product::getProductsWithStatusInWork($master);
        if (count($products) > 1) {
            return $this->render(
                'conflict',
                [
                    'master_id' => $master['id'],
                ]
            );
        }
        if (count($products) == 1) {
            $product = $products[0];
            $historyProductUchastok = (new \yii\db\Query())
                ->select(['historyProductUchastok'])
                ->from('workshop')
                ->where(['masterWorkshop' => $master['ufCrm14Workshop']])
                ->one()['historyProductUchastok'];
            $historyFields = [
                'ufCrm16Master' => $master['id'],
                'ufCrm16Status' => 882, // в работе
                'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
                'parentId187' => $product->id,
                'ufCrm16Operation' => 922, // взял в работу
                'ufCrm16Uchastok' => $historyProductUchastok // участок
            ];
            HistoryProduct::add($historyFields);
            return $this->render(
                'main',
                [
                    'product_name' => $product->title,
                    'product_id' => $product->id,
                    'link' => $product->link
//                    'master_id' => $master['id'],
                ]
            );
        }
        $productOrder = [
            'ufCrm8_1701952418411' => 'ASC', // сортировать по возрастанию
        ];
        if ($products == []) {
            $productFilter = [
                'ufCrm8_1705585967731' => 766, // Срочность ***
                'ufCrm8Master' => $master['id'],
                'ufCrm8Status' => 900, // в очереди
            ];
            $products = Product::list($productFilter, $productOrder);
        }
        if ($products == []) {
            $productFilter = [
                'ufCrm8_1705585967731' => 764, // Срочность **
                'ufCrm8Master' => $master['id'],
                'ufCrm8Status' => 900, // в очереди
            ];
            $products = Product::list($productFilter, $productOrder);
        }
        if ($products == []) {
            $productFilter = [
                'ufCrm8_1705585967731' => 754, // Срочность *
                'ufCrm8Master' => $master['id'],
                'ufCrm8Status' => 900, // в очереди
            ];
            $products = Product::list($productFilter, $productOrder);
        }
        if ($products == []) {
            $productFilter = [
                'ufCrm8_1705585967731' => null, // Срочность null
                'ufCrm8Master' => $master['id'],
                'ufCrm8Status' => 900, // в очереди
            ];
            $products = Product::list($productFilter, $productOrder);
        }
        $stageId = (new \yii\db\Query())
            ->select(['stageId'])
            ->from('workshop')
            ->where(['masterWorkshop' => $master['ufCrm14Workshop']])
            ->one()['stageId'];
        if ($products == []) {
            $productFilter = [
                'ufCrm8_1705585967731' => 766, // Срочность ***
                'ufCrm8Status' => 896, // на складе
                'stageId' => $stageId, // стадия/участок
            ];
            $products = Product::list($productFilter, $productOrder);
        }
        if ($products == []) {
            $productFilter = [
                'ufCrm8_1705585967731' => 764, // Срочность **
                'ufCrm8Status' => 896, // на складе
                'stageId' => $stageId, // стадия/участок
            ];
            $products = Product::list($productFilter, $productOrder);
        }
        if ($products == []) {
            $productFilter = [
                'ufCrm8_1705585967731' => 754, // Срочность *
                'ufCrm8Status' => 896, // на складе
                'stageId' => $stageId, // стадия/участок
            ];
            $products = Product::list($productFilter, $productOrder);
        }
        if ($products == []) {
            $productFilter = [
                'ufCrm8_1705585967731' => null, // Срочность null
                'ufCrm8Status' => 896, // на складе
                'stageId' => $stageId, // стадия/участок
            ];
            $products = Product::list($productFilter, $productOrder);
        }
        if (count($products) == 0) {
            return $this->render(
                'noProduct',
                [
                    'master_id' => $master['id']
                ]
            );
        }
        if (count($products) >= 1) {
            $product = $products[0];
            $productFields = [
                'ufCrm8Status' => 898, // в работе
                'ufCrm8Master' => $master['id'],
            ];
            Product::update($product->id, $productFields);

            $historyProductUchastok = (new \yii\db\Query())
                ->select(['historyProductUchastok'])
                ->from('workshop')
                ->where(['masterWorkshop' => $master['ufCrm14Workshop']])
                ->one()['historyProductUchastok'];
            $historyFields = [
                'ufCrm16Master' => $master['id'],
                'ufCrm16Status' => 882, // в работе
                'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
                'parentId187' => $product->id,
                'ufCrm16Operation' => 922, // взял в работу
                'ufCrm16Uchastok' => $historyProductUchastok // участок
            ];
            HistoryProduct::add($historyFields);

            return $this->render(
                'main',
                [
                    'product_name' => $product->title,
                    'product_id' => $product->id,
                    'link' => $product->link
//                    'master_id' => $product->masterId
                ]
            );
        }
    }

    public function actionTakeFollowingProduct()
    {
        $usersId = Yii::$app->user->id;
        $master = Master3::getMasterByUserId($usersId);
        $orderHistoryProduct = ['id' => 'DESC']; // В порядке убывания
        $filterHistoryProduct = [
            'ufCrm16Master' => $master['id'],
        ];
        $historys = HistoryProduct::list($filterHistoryProduct, $orderHistoryProduct);

        if ($historys != []) {
            $history = $historys[0];
            $fieldsHistory = [
                'ufCrm16Status' => 880 // на складе
            ];
            HistoryProduct::update($history->id, $fieldsHistory);
            Yii::$app->response->redirect('https://test.mysmartautomation.ru/productManagement/workplace/start');
        }
    }
}
