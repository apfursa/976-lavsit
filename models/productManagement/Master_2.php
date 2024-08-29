<?php

namespace app\models\productManagement;

use yii\helpers\ArrayHelper;
use yii\base\Model;
use wm\yii\db\ActiveRecord;
use Yii;

// Мастер

/**
 * Class Master
 * @property string $name
 * @property string $last_name
 * @property string $sur_name
 * @property string $login
 * @property string $pass
 * @property string $usersId
 * @property string $subdivisionId
 * @property string $subdivision
 * @property string $date_create
 * @package app\controllers\productManagement
 */
class Master_2 extends Model
{
    public function start($continue = false)
    {
        Yii::warning('Master_2','start_Master_2');
        $usersId = Yii::$app->user->id;
        $master = $this->getMasterByUserId($usersId);
        $masterId = $master['id'];
        $masterWorkshop = $master['ufCrm14Workshop']; // участок

        // Запись даты и времени начала работы в СП "Учет рабочего времени"
        $otvet = $this->uchetRabochegoVremeny($master, $continue);

        if ($otvet == 'ok') {
            $component = new \wm\b24tools\b24Tools();
            $b24App = $component->connectFromAdmin();
            $obB24 = new \Bitrix24\B24Object($b24App);
            $answerB24 = $obB24->client->call(
                'crm.item.list',
                [
                    'entityTypeId' => 187, //СП Производство
                    'order' => ['id' => 'ASC'], // сортировать по возрастанию
                    'filter' => [
                        'ufCrm8Master' => $masterId,
                        'ufCrm8Status' => 898 // в работе
                    ]
                ]
            );
            $arrProduct = $answerB24['result']['items'];
            if (count($arrProduct) > 1) {
                return [
                    'page' => 3
                ];
            }
            if (count($arrProduct) == 1) {
                $component = new \wm\b24tools\b24Tools();
                $b24App = $component->connectFromAdmin();
                $obB24 = new \Bitrix24\B24Object($b24App);
                $history = $obB24->client->call(
                    'crm.item.add',
                    [
                        'entityTypeId' => 1042, // СП История изделия
                        'fields' => [
                            'ufCrm16Master' => $masterId,
                            'ufCrm16Status' => 882, // в работе
                            'ufCrm16Srochnost' => $arrProduct[0]['ufCrm8_1705585967731'], // срочность
                            'ufCrm16Product' => $arrProduct[0]['id'],
                            'ufCrm16Operation' => 922, // взял в работу
                            'ufCrm16Uchastok' => $masterWorkshop // участок
                        ]
                    ]
                );
                return [
                    'page' => 2,
                    'product_name' => $arrProduct[0]['title'],
                    'product_id' => $arrProduct[0]['id'],
                    'master_id' => $arrProduct[0]['ufCrm8Master'],

                ];
            }
            if ($arrProduct == []) {
                $component = new \wm\b24tools\b24Tools();
                $b24App = $component->connectFromAdmin();
                $obB24 = new \Bitrix24\B24Object($b24App);
                $answerB24 = $obB24->client->call(
                    'crm.item.list',
                    [
                        'entityTypeId' => 187, //СП Производство
                        'order' => [
                            'ufCrm8_1701952418411' => 'ASC' // Дата готовности
                        ],
                        'filter' => [
                            'ufCrm8_1705585967731' => 766, // Срочность ***
                            'ufCrm8Master' => $masterId,
                            'ufCrm8Status' => 900, // в очереди
                        ]
                    ]
                );
            }
            if ($answerB24['result']['items'] == []) {
                $component = new \wm\b24tools\b24Tools();
                $b24App = $component->connectFromAdmin();
                $obB24 = new \Bitrix24\B24Object($b24App);
                $answerB24 = $obB24->client->call(
                    'crm.item.list',
                    [
                        'entityTypeId' => 187, //СП Производство
                        'order' => [
                            'ufCrm8_1701952418411' => 'ASC' // Дата готовности
                        ],
                        'filter' => [
                            'ufCrm8_1705585967731' => 764, // Срочность **
                            'ufCrm8Master' => $masterId,
                            'ufCrm8Status' => 900, // в очереди
                        ]
                    ]
                );
            }
            if ($answerB24['result']['items'] == []) {
                $component = new \wm\b24tools\b24Tools();
                $b24App = $component->connectFromAdmin();
                $obB24 = new \Bitrix24\B24Object($b24App);
                $answerB24 = $obB24->client->call(
                    'crm.item.list',
                    [
                        'entityTypeId' => 187, //СП Производство
                        'order' => [
                            'ufCrm8_1701952418411' => 'ASC' // Дата готовности
                        ],
                        'filter' => [
                            'ufCrm8_1705585967731' => 754, // Срочность *
                            'ufCrm8Master' => $masterId,
                            'ufCrm8Status' => 900, // в очереди
                        ]
                    ]
                );
            }
            $query = (new \yii\db\Query())->select(['stageId'])->from('workshop')->where(['masterWorkshop' => $masterWorkshop]);
            $data = $query->one();
            $stageId = $data['stageId'];
            if ($answerB24['result']['items'] == []) {
                $component = new \wm\b24tools\b24Tools();
                $b24App = $component->connectFromAdmin();
                $obB24 = new \Bitrix24\B24Object($b24App);
                $answerB24 = $obB24->client->call(
                    'crm.item.list',
                    [
                        'entityTypeId' => 187, //СП Производство
                        'order' => [
                            'ufCrm8_1701952418411' => 'ASC' // Дата готовности
                        ],
                        'filter' => [
                            'ufCrm8_1705585967731' => 766, // Срочность ***
                            'ufCrm8Status' => 896, // на складе
                            'stageId' => $stageId, // стадия/участок
                        ]
                    ]
                );
            }
            if ($answerB24['result']['items'] == []) {
                $component = new \wm\b24tools\b24Tools();
                $b24App = $component->connectFromAdmin();
                $obB24 = new \Bitrix24\B24Object($b24App);
                $answerB24 = $obB24->client->call(
                    'crm.item.list',
                    [
                        'entityTypeId' => 187, //СП Производство
                        'order' => [
                            'ufCrm8_1701952418411' => 'ASC' // Дата готовности
                        ],
                        'filter' => [
                            'ufCrm8_1705585967731' => 764, // Срочность **
                            'ufCrm8Status' => 896, // на складе
                            'stageId' => $stageId, // стадия/участок
                        ]
                    ]
                );
            }
            if ($answerB24['result']['items'] == []) {
                $component = new \wm\b24tools\b24Tools();
                $b24App = $component->connectFromAdmin();
                $obB24 = new \Bitrix24\B24Object($b24App);
                $answerB24 = $obB24->client->call(
                    'crm.item.list',
                    [
                        'entityTypeId' => 187, //СП Производство
                        'order' => [
                            'ufCrm8_1701952418411' => 'ASC' // Дата готовности
                        ],
                        'filter' => [
                            'ufCrm8_1705585967731' => 754, // Срочность *
                            'ufCrm8Status' => 896, // на складе
                            'stageId' => $stageId, // стадия/участок
                        ]
                    ]
                );
            }
            $arrProduct = $answerB24['result']['items'];
            if (count($arrProduct) == 0) {
                return [
                    'page' => 9,
                    'master_id' => $masterId,
                ];
            }
            if (count($arrProduct) >= 1) {
                $component = new \wm\b24tools\b24Tools();
                $b24App = $component->connectFromAdmin();
                $obB24 = new \Bitrix24\B24Object($b24App);
                $answerB24 = $obB24->client->call(
                    'crm.item.update',
                    [
                        'entityTypeId' => 187, //СП Производство
                        'id' => $arrProduct[0]['id'],
                        'fields' => [
                            'ufCrm8Status' => 898, // в работе
                            'ufCrm8Master' => $masterId,
                        ]
                    ]
                );
                $component = new \wm\b24tools\b24Tools();
                $b24App = $component->connectFromAdmin();
                $obB24 = new \Bitrix24\B24Object($b24App);
                $history = $obB24->client->call(
                    'crm.item.add',
                    [
                        'entityTypeId' => 1042, // СП История изделия
                        'fields' => [
                            'ufCrm16Master' => $masterId,
                            'ufCrm16Status' => 882, // в работе
                            'ufCrm16Srochnost' => $arrProduct[0]['ufCrm8_1705585967731'], // срочность
                            'ufCrm16Product' => $arrProduct[0]['id'],
                            'ufCrm16Operation' => 922, // взял в работу
                            'ufCrm16Uchastok' => $masterWorkshop // участок
                        ]
                    ]
                );
//                $this->addHistoryProduct($arrDate);
                return [
                    'page' => 2,
                    'product_name' => $arrProduct[0]['title'],
                    'product_id' => $arrProduct[0]['id'],
                    'master_id' => $arrProduct[0]['ufCrm8Master']
                ];
            }
        }
        if ($otvet == 'beginningWorkingDay') {
            $component = new \wm\b24tools\b24Tools();
            $b24App = $component->connectFromAdmin();
            $obB24 = new \Bitrix24\B24Object($b24App);
            $history = $obB24->client->call(
                'crm.item.list',
                [
                    'entityTypeId' => 1042, //СП "История изделия"
                    'order' => ['id' => 'DESC'], // сортировать по убыванию
                    'filter' => ['ufCrm16Master' => $masterId,]
                ]
            )['result']['items'][0];
            // Если статус последней записи == "технологическая пауза"
            if ($history['ufCrm16Status'] == 886) {


                $component = new \wm\b24tools\b24Tools();
                $b24App = $component->connectFromAdmin();
                $obB24 = new \Bitrix24\B24Object($b24App);
                $product = $obB24->client->call(
                    'crm.item.list',
                    [
                        'entityTypeId' => 187, //СП Производство
                        'order' => ['id' => 'ASC'], // сортировать по возрастанию
                        'filter' => ['id' => $history['ufCrm16Product']]
                    ]
                )['result']['items'][0];

                $component = new \wm\b24tools\b24Tools();
                $b24App = $component->connectFromAdmin();
                $obB24 = new \Bitrix24\B24Object($b24App);
                $obB24->client->call(
                    'crm.item.add',
                    [
                        'entityTypeId' => 1042, // СП История изделия
                        'fields' => [
                            'ufCrm16Master' => $masterId,
                            'ufCrm16Status' => 886, // технологическая пауза
                            'ufCrm16Srochnost' => $product['ufCrm8_1705585967731'], // срочность
                            'ufCrm16Product' => $product['id'],
                            'ufCrm16Operation' => 906, // начало рабочего дня
                            'ufCrm16Uchastok' => $masterWorkshop // участок
                        ]
                    ]
                );

                return [
                    'page' => 'technologicalPause',
                    'product_name' => $product['title'],
                    'product_id' => $product['id'],
                    'master_id' => $product['ufCrm8Master'],
                ];
            } else {
                $component = new \wm\b24tools\b24Tools();
                $b24App = $component->connectFromAdmin();
                $obB24 = new \Bitrix24\B24Object($b24App);
                $answerB24 = $obB24->client->call(
                    'crm.item.list',
                    [
                        'entityTypeId' => 187, //СП Производство
                        'order' => ['id' => 'ASC'], // сортировать по возрастанию
                        'filter' => [
                            'ufCrm8Master' => $masterId,
                            'ufCrm8Status' => 898 // в работе
                        ]
                    ]
                );
                $arrProduct = $answerB24['result']['items'];
                if (count($arrProduct) > 1) {
                    return [
                        'page' => 3
                    ];
                }
                if (count($arrProduct) == 1) {
                    $component = new \wm\b24tools\b24Tools();
                    $b24App = $component->connectFromAdmin();
                    $obB24 = new \Bitrix24\B24Object($b24App);
                    $history = $obB24->client->call(
                        'crm.item.add',
                        [
                            'entityTypeId' => 1042, // СП История изделия
                            'fields' => [
                                'ufCrm16Master' => $masterId,
                                'ufCrm16Status' => 882, // в работе
                                'ufCrm16Srochnost' => $arrProduct[0]['ufCrm8_1705585967731'], // срочность
                                'ufCrm16Product' => $arrProduct[0]['id'],
                                'ufCrm16Operation' => 906, // начало рабочего дня
                                'ufCrm16Uchastok' => $masterWorkshop // участок
                            ]
                        ]
                    );
                    return [
                        'page' => 2,
                        'product_name' => $arrProduct[0]['title'],
                        'product_id' => $arrProduct[0]['id'],
                        'master_id' => $arrProduct[0]['ufCrm8Master'],
                    ];
                }
                if ($arrProduct == []) {
                    $component = new \wm\b24tools\b24Tools();
                    $b24App = $component->connectFromAdmin();
                    $obB24 = new \Bitrix24\B24Object($b24App);
                    $answerB24 = $obB24->client->call(
                        'crm.item.list',
                        [
                            'entityTypeId' => 187, //СП Производство
                            'order' => [
                                'ufCrm8_1701952418411' => 'ASC' // Дата готовности
                            ],
                            'filter' => [
                                'ufCrm8_1705585967731' => 766, // Срочность ***
                                'ufCrm8Master' => $masterId,
                                'ufCrm8Status' => 900, // в очереди
                            ]
                        ]
                    );
                }
                if ($answerB24['result']['items'] == []) {
                    $component = new \wm\b24tools\b24Tools();
                    $b24App = $component->connectFromAdmin();
                    $obB24 = new \Bitrix24\B24Object($b24App);
                    $answerB24 = $obB24->client->call(
                        'crm.item.list',
                        [
                            'entityTypeId' => 187, //СП Производство
                            'order' => [
                                'ufCrm8_1701952418411' => 'ASC' // Дата готовности
                            ],
                            'filter' => [
                                'ufCrm8_1705585967731' => 764, // Срочность **
                                'ufCrm8Master' => $masterId,
                                'ufCrm8Status' => 900, // в очереди
                            ]
                        ]
                    );
                }
                if ($answerB24['result']['items'] == []) {
                    $component = new \wm\b24tools\b24Tools();
                    $b24App = $component->connectFromAdmin();
                    $obB24 = new \Bitrix24\B24Object($b24App);
                    $answerB24 = $obB24->client->call(
                        'crm.item.list',
                        [
                            'entityTypeId' => 187, //СП Производство
                            'order' => [
                                'ufCrm8_1701952418411' => 'ASC' // Дата готовности
                            ],
                            'filter' => [
                                'ufCrm8_1705585967731' => 754, // Срочность *
                                'ufCrm8Master' => $masterId,
                                'ufCrm8Status' => 900, // в очереди
                            ]
                        ]
                    );
                }
                $query = (new \yii\db\Query())->select(['stageId'])->from('workshop')->where(['masterWorkshop' => $masterWorkshop]);
                $data = $query->one();
                $stageId = $data['stageId'];
                if ($answerB24['result']['items'] == []) {
                    $component = new \wm\b24tools\b24Tools();
                    $b24App = $component->connectFromAdmin();
                    $obB24 = new \Bitrix24\B24Object($b24App);
                    $answerB24 = $obB24->client->call(
                        'crm.item.list',
                        [
                            'entityTypeId' => 187, //СП Производство
                            'order' => [
                                'ufCrm8_1701952418411' => 'ASC' // Дата готовности
                            ],
                            'filter' => [
                                'ufCrm8_1705585967731' => 766, // Срочность ***
                                'ufCrm8Status' => 896, // на складе
                                'stageId' => $stageId, // стадия/участок
                            ]
                        ]
                    );
//                    $productFilter = [
//                        'ufCrm8_1705585967731' => 766, // Срочность ***
//                        'ufCrm8Status' => 896, // на складе
//                        'stageId' => $stageId, // стадия/участок
//                    ];
//                    $productOrder = [
//                        'ufCrm8_1701952418411' => 'ASC' // Дата готовности
//                    ];
//                    $products = Product::list($productFilter, $productOrder);
                }
                if ($answerB24['result']['items'] == []) {
                    $component = new \wm\b24tools\b24Tools();
                    $b24App = $component->connectFromAdmin();
                    $obB24 = new \Bitrix24\B24Object($b24App);
                    $answerB24 = $obB24->client->call(
                        'crm.item.list',
                        [
                            'entityTypeId' => 187, //СП Производство
                            'order' => [
                                'ufCrm8_1701952418411' => 'ASC' // Дата готовности
                            ],
                            'filter' => [
                                'ufCrm8_1705585967731' => 764, // Срочность **
                                'ufCrm8Status' => 896, // на складе
                                'stageId' => $stageId, // стадия/участок
                            ]
                        ]
                    );
                }
                if ($answerB24['result']['items'] == []) {
                    $component = new \wm\b24tools\b24Tools();
                    $b24App = $component->connectFromAdmin();
                    $obB24 = new \Bitrix24\B24Object($b24App);
                    $answerB24 = $obB24->client->call(
                        'crm.item.list',
                        [
                            'entityTypeId' => 187, //СП Производство
                            'order' => [
                                'ufCrm8_1701952418411' => 'ASC' // Дата готовности
                            ],
                            'filter' => [
                                'ufCrm8_1705585967731' => 754, // Срочность *
                                'ufCrm8Status' => 896, // на складе
                                'stageId' => $stageId, // стадия/участок
                            ]
                        ]
                    );
                }
                $arrProduct = $answerB24['result']['items'];
                if (count($arrProduct) == 0) {
                    return [
                        'page' => 9,
                        'master_id' => $masterId,
                    ];
                }
                if (count($arrProduct) >= 1) {
                    $component = new \wm\b24tools\b24Tools();
                    $b24App = $component->connectFromAdmin();
                    $obB24 = new \Bitrix24\B24Object($b24App);
                    $answerB24 = $obB24->client->call(
                        'crm.item.update',
                        [
                            'entityTypeId' => 187, //СП Производство
                            'id' => $arrProduct[0]['id'],
                            'fields' => [
                                'ufCrm8Status' => 898, // в работе
                                'ufCrm8Master' => $masterId,
                            ]
                        ]
                    );
                    $component = new \wm\b24tools\b24Tools();
                    $b24App = $component->connectFromAdmin();
                    $obB24 = new \Bitrix24\B24Object($b24App);
                    $history = $obB24->client->call(
                        'crm.item.add',
                        [
                            'entityTypeId' => 1042, // СП История изделия
                            'fields' => [
                                'ufCrm16Master' => $masterId,
                                'ufCrm16Status' => 882, // в работе
                                'ufCrm16Srochnost' => $arrProduct[0]['ufCrm8_1705585967731'], // срочность
                                'ufCrm16Product' => $arrProduct[0]['id'],
                                'ufCrm16Operation' => 922, // взял в работу
                                'ufCrm16Uchastok' => $masterWorkshop // участок
                            ]
                        ]
                    );
                    return [
                        'page' => 2,
                        'product_name' => $arrProduct[0]['title'],
                        'product_id' => $arrProduct[0]['id'],
                        'master_id' => $arrProduct[0]['ufCrm8Master']
                    ];
                }
            }

        }
        if ($otvet == 'continue') {
            return [
                'page' => 'continue',
                'master_id' => $masterId
            ];
        }
        if ($otvet == 'perezapisat_vremya') {
            return [
                'page' => 10
            ];
        }

    }

    public function getMasterByUserId($usersId)
    {
        Yii::warning('Master_2','getMasterByUserId_Master_2');
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

    // Запись даты и времени начала работы в СП "Учет рабочего времени"
    public function uchetRabochegoVremeny($master, $continue = false)
    {
        Yii::warning('Master_2','uchetRabochegoVremeny_Master_2');
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $arrData = [
            'masterId' => $master['id'],
            'date' => $date,
            'time' => $time,
            'masterWorkshop' => $master['ufCrm14Workshop']
        ];

        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => 1046, // СП "Учет рабочего времени"
                'order' => ['id' => 'DESC'], // В порядке убывания
                'filter' => ['ufCrm18Master' => $master['id']]
            ]
        );
        $uchetRabochegoVremeny = $answerB24['result']['items'];
        if ($uchetRabochegoVremeny == []) {
            $this->addEntryToSpUchetRabochegoVremeny($arrData);
            return 'ok';
        } else {
//            $maxId = max(ArrayHelper::getColumn($uchetRabochegoVremeny, 'id'));
//            $arrIndex = ArrayHelper::index($uchetRabochegoVremeny, 'id');

            // Если последний раз завершено автоматически
            if ($uchetRabochegoVremeny[0]['ufCrm18TerminatioType'] == 892) {
                // Написать логику
                return 'perezapisat_vremya';
            }

            // Если последний раз завершено НЕ автоматически
            if ($uchetRabochegoVremeny[0]['ufCrm18UshelTime'] != '') {
                $this->addEntryToSpUchetRabochegoVremeny($arrData);
                return 'beginningWorkingDay'; // начало рабочего дня
            }

            if ($uchetRabochegoVremeny[0]['ufCrm18UshelTime'] == '' && $continue !== false) {
                return 'ok';
            } else {
                return 'continue';
            }

        }
    }

    public function endWorkingDay($product_id, $master_id)
    {
        Yii::warning('Master_2','endWorkingDay_Master_2');
        if ($product_id) {
            $component = new \wm\b24tools\b24Tools();
            $b24App = $component->connectFromAdmin();
            $obB24 = new \Bitrix24\B24Object($b24App);
            $product = $obB24->client->call(
                'crm.item.list',
                [
                    'entityTypeId' => 187, //СП Производство
                    'filter' => [
                        'id' => $product_id,
                    ]
                ]
            )['result']['items'][0];

            $historyProductUchastok = (new \yii\db\Query())->select(['historyProductUchastok'])->from('workshop')->where(['stageId' => $product['stageId']])->one()['historyProductUchastok'];
            $historyProductStatus = (new \yii\db\Query())->select(['ufCrm16Status'])->from('statusProduct')->where(['ufCrm8Status' => $product['ufCrm8Status']])->one()['ufCrm16Status'];
            $component = new \wm\b24tools\b24Tools();
            $b24App = $component->connectFromAdmin();
            $obB24 = new \Bitrix24\B24Object($b24App);
            $history = $obB24->client->call(
                'crm.item.add',
                [
                    'entityTypeId' => 1042, // СП История изделия
                    'fields' => [
                        'ufCrm16Product' => $product_id,
                        'ufCrm16Master' => $product['ufCrm8Master'],
                        'ufCrm16Status' => $historyProductStatus, // статус
                        'ufCrm16Operation' => 908, // конец рабочего дня
                        'ufCrm16Uchastok' => $historyProductUchastok, // участок
                        'ufCrm16Srochnost' => $product['ufCrm8_1705585967731'], // срочность
                    ]
                ]
            );

//        $usersId = Yii::$app->user->id;
//        $master = $this->getMaster($usersId);
//        $masterId = $master['id'];
//        $masterWorkshop = $master['ufCrm14Workshop']; // участок

            // Запись даты и времени окончания работы в СП "Учет рабочего времени"
            $this->updateEntryToSpUchetRabochegoVremeny($product['ufCrm8Master']);
        }
        if ($master_id) {
            $this->updateEntryToSpUchetRabochegoVremeny($master_id);
        }


    }

    public function returnProduct($product_id)
    {
        Yii::warning('Master_2','returnProduct_Master_2');
//        $usersId = Yii::$app->user->id;
//        $masterId = $this->getMasterId($usersId);

//        $component = new \wm\b24tools\b24Tools();
//        $b24App = $component->connectFromAdmin();
//        $obB24 = new \Bitrix24\B24Object($b24App);
//        $answerB24 = $obB24->client->call(
//            'crm.item.list',
//            [
//                'entityTypeId' => 187, //СП Производство
//                'filter' => [
//                    'id' => $product_id
//                ]
//            ]
//        );
//        $arrProduct = $answerB24['result']['items'];

        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $product = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => 187, //СП Производство
                'filter' => [
                    'id' => $product_id,
                ]
            ]
        )['result']['items'][0];
        $workshopId = (new \yii\db\Query())->select(['id'])->from('workshop')->where(['stageId' => $product['stageId']])->one()['id'];
        $historyProductUchastok = (new \yii\db\Query())->select(['historyProductUchastok'])->from('workshop')->where(['id' => $workshopId - 1])->one()['historyProductUchastok'];
        $previousStage = (new \yii\db\Query())->select(['stageId'])->from('workshop')->where(['id' => $workshopId - 1])->one()['stageId'];


        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'crm.item.update',
            [
                'entityTypeId' => 187, //СП Производство
                'id' => $product_id,
                'fields' => [
                    'ufCrm8Master' => '',
                    'ufCrm8Status' => 896, // на складе
//                    'stageId' => $previousStage // Перевод изделия на предыдущую стадию / на предыдущий участок
                ]
            ]
        );

        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $history = $obB24->client->call(
            'crm.item.add',
            [
                'entityTypeId' => 1042, // СП История изделия
                'fields' => [
                    'ufCrm16Product' => $product_id,
                    'ufCrm16Master' => $product['ufCrm8Master'],
                    'ufCrm16Status' => 880, // на складе
                    'ufCrm16Operation' => 918, // вернул на предыдущий этап
                    'ufCrm16Uchastok' => $historyProductUchastok, // предыдущий участок
                    'ufCrm16Srochnost' => $product['ufCrm8_1705585967731'], // срочность
                ]
            ]
        );
        return [
            'master_id' => $product['ufCrm8Master']
        ];

    }

    public function technologicalPauseStart($product_id)
    {
        Yii::warning('Master_2','technologicalPauseStart_Master_2');
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $product = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => 187, //СП Производство
                'filter' => [
                    'id' => $product_id,
                ]
            ]
        )['result']['items'][0];
        $workshopId = (new \yii\db\Query())->select(['id'])->from('workshop')->where(['stageId' => $product['stageId']])->one()['id'];
        $historyProductUchastok = (new \yii\db\Query())->select(['historyProductUchastok'])->from('workshop')->where(['id' => $workshopId])->one()['historyProductUchastok'];
        $previousStage = (new \yii\db\Query())->select(['stageId'])->from('workshop')->where(['id' => $workshopId - 1])->one()['stageId'];


        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'crm.item.update',
            [
                'entityTypeId' => 187, //СП Производство
                'id' => $product_id,
                'fields' => [
//                    'ufCrm8Master' => '',
                    'ufCrm8Status' => 902, // технологическая пауза
//                    'stageId' => $previousStage // Перевод изделия на предыдущую стадию / на предыдущий участок
                ]
            ]
        );

        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $history = $obB24->client->call(
            'crm.item.add',
            [
                'entityTypeId' => 1042, // СП История изделия
                'fields' => [
                    'ufCrm16Product' => $product_id,
                    'ufCrm16Master' => $product['ufCrm8Master'],
                    'ufCrm16Status' => 886, // технологическая пауза
                    'ufCrm16Operation' => 914, // поставил на технологическую паузу
                    'ufCrm16Uchastok' => $historyProductUchastok, // текущий участок
                    'ufCrm16Srochnost' => $product['ufCrm8_1705585967731'], // срочность
                ]
            ]
        );
        return [
            'product_name' => $product['title'],
            'product_id' => $product['id']
        ];
    }

    public function technologicalPauseEnd($product_id)
    {
        Yii::warning('Master_2','technologicalPauseEnd_Master_2');
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $product = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => 187, //СП Производство
                'filter' => [
                    'id' => $product_id,
                ]
            ]
        )['result']['items'][0];
        $workshopId = (new \yii\db\Query())->select(['id'])->from('workshop')->where(['stageId' => $product['stageId']])->one()['id'];
        $historyProductUchastok = (new \yii\db\Query())->select(['historyProductUchastok'])->from('workshop')->where(['id' => $workshopId])->one()['historyProductUchastok'];
        $previousStage = (new \yii\db\Query())->select(['stageId'])->from('workshop')->where(['id' => $workshopId - 1])->one()['stageId'];


        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'crm.item.update',
            [
                'entityTypeId' => 187, //СП Производство
                'id' => $product_id,
                'fields' => [
//                    'ufCrm8Master' => '',
                    'ufCrm8Status' => 898, // в работе
//                    'stageId' => $previousStage // Перевод изделия на предыдущую стадию / на предыдущий участок
                ]
            ]
        );

        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $history = $obB24->client->call(
            'crm.item.add',
            [
                'entityTypeId' => 1042, // СП История изделия
                'fields' => [
                    'ufCrm16Product' => $product_id,
                    'ufCrm16Master' => $product['ufCrm8Master'],
                    'ufCrm16Status' => 882, // в работе
                    'ufCrm16Operation' => 916, // снял с технологической паузы
                    'ufCrm16Uchastok' => $historyProductUchastok, // текущий участок
                    'ufCrm16Srochnost' => $product['ufCrm8_1705585967731'], // срочность
                ]
            ]
        );
        return [
            'product_name' => $product['title'],
            'product_id' => $product['id']
        ];
    }


    public function done($product_id)
    {
        Yii::warning('Master_2','done_Master_2');
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $product = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => 187, //СП Производство
                'filter' => [
                    'id' => $product_id,
                ]
            ]
        )['result']['items'][0];
        $workshopId = (new \yii\db\Query())->select(['id'])->from('workshop')->where(['stageId' => $product['stageId']])->one()['id'];
        $historyProductUchastok = (new \yii\db\Query())->select(['historyProductUchastok'])->from('workshop')->where(['id' => $workshopId + 1])->one()['historyProductUchastok'];
        $nextStage = (new \yii\db\Query())->select(['stageId'])->from('workshop')->where(['id' => $workshopId + 1])->one()['stageId'];

        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'crm.item.update',
            [
                'entityTypeId' => 187, //СП Производство
                'id' => $product_id,
                'fields' => [
                    'ufCrm8Master' => '',
                    'ufCrm8Status' => 896, // на складе
//                    'stageId' => $nextStage // Перевод изделия на следующую стадию / на следующий участок
                ]
            ]
        );

        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $history = $obB24->client->call(
            'crm.item.add',
            [
                'entityTypeId' => 1042, // СП История изделия
                'fields' => [
                    'ufCrm16Product' => $product_id,
                    'ufCrm16Master' => $product['ufCrm8Master'],
                    'ufCrm16Status' => 880, // на складе
                    'ufCrm16Operation' => 920, // перевел на следующий этап
                    'ufCrm16Uchastok' => $historyProductUchastok, // следующий участок
                    'ufCrm16Srochnost' => $product['ufCrm8_1705585967731'], // срочность
                ]
            ]
        );
        return [
            'master_id' => $product['ufCrm8Master']
        ];

    }

    public function updateEntryToSpUchetRabochegoVremeny($masterId)
    {
        Yii::warning('Master_2','updateEntryToSpUchetRabochegoVremeny_Master_2');
        $date = date('Y-m-d');
        $time = date('H:i:s');

        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => 1046, // СП "Учет рабочего времени"
                'order' => ['id' => 'DESC'], // В порядке убывания
                'filter' => ['ufCrm18Master' => $masterId]
            ]
        );
        $arrOfElements = $answerB24['result']['items'];
        if ($arrOfElements) {
            $id = $arrOfElements[0]['id'];

            $component = new \wm\b24tools\b24Tools();
            $b24App = $component->connectFromAdmin();
            $obB24 = new \Bitrix24\B24Object($b24App);
            $answerB24 = $obB24->client->call(
                'crm.item.update',
                [
                    'entityTypeId' => 1046, // СП "Учет рабочего времени"
                    'id' => $id,
                    'fields' => [
                        'ufCrm18TerminatioType' => 890, // Завершено пользователем
                        'ufCrm18UshelData' => $date,
                        'ufCrm18UshelTime' => $time
                    ]
                ]
            );
        }
    }

    public function addEntryToSpUchetRabochegoVremeny($arrData)
    {
        Yii::warning('Master_2','addEntryToSpUchetRabochegoVremeny_Master_2');
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'crm.item.add',
            [
                'entityTypeId' => 1046, // СП "Учет рабочего времени"
                'fields' => [
                    'ufCrm18Master' => $arrData['masterId'],
                    'ufCrm18PrishelDate' => $arrData['date'],
                    'ufCrm18PrishelTime' => $arrData['time'],
//                    'ufCrm18Uchastok' => $arrData['masterWorkshop'] // нужно переделывать тип поля в СП
                ]
            ]
        );
    }

    public function getArrProductFromSp($entityTypeId, $filter)
    {
        Yii::warning('Master_2','getArrProductFromSp_Master_2');
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => $entityTypeId, //СП Производство
                'order' => ['id' => 'ASC'], // сортировать по возрастанию
                'filter' => $filter
            ]
        );
        return $answerB24;
    }
}