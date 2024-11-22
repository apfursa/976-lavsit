<?php

namespace app\controllers\test;

use Yii;
use yii\helpers\ArrayHelper;

class DealController extends \wm\admin\controllers\RestController
{
    public function actionCrm_deal_get($id)
    {
//        $arData = ["ID" => $id];
//        $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.deal.get.json';
//        $resultDeal = self::sendToBx($url, json_encode($arData));
//        $resultDeal = json_decode($resultDeal,1);
//        $resultDateDeal = date('d.m.Y',  strtotime($resultDeal['result']["UF_CRM_1571825141958"].' -4 days'));
//        return $resultDateDeal;

        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $request = $obB24->client->call(
            'crm.deal.get',
            [
                'id' => $id
            ]
        );
        return $request;
    }

    public function actionTasks_task_list()
    {
        //ищем задачу поставщика
        $arData = ["filter" => ["TITLE" => 32672],
            "select" => [
                "ID",
                "TITLE",
                "DESCRIPTION",
                "CREATED_BY",
                "RESPONSIBLE_ID",
                "CREATED_DATE",
                "CHANGED_DATE",
                "DEADLINE"
            ],
            "order" => ["ID" => "asc"]];
        $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/tasks.task.list.json';
        $result = self::sendToBx($url, json_encode($arData));
        $result = json_decode($result, 1);
        return $result;
    }

    public function actionCrm_item_list_h()
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $history = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => 1042, //СП "История изделия"
                'order' => ['id' => 'DESC'], // сортировать по убыванию
                //                'filter' => ['parentId187' => 9788,],
            ]
        )['result']['items'];
        return $history;
    }

    public function actionCrm_deal_fields()
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $request = $obB24->client->call('crm.deal.fields');
        return $request;
    }

    public function actionCrm_deal_productrows_get()
    {
        // ищем товары
        $arData = ["ID" => 32764];
        $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.deal.productrows.get.json';
        $resultOrder = self::sendToBx($url, json_encode($arData));
        $resultOrder = json_decode($resultOrder, 1);
        return $resultOrder;
    }
    public function actionCrm_product_get($prodId)
    {
        $arData = ["ID" => $prodId];
        // ищем товар
        $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.product.get.json';
        $resultProd = self::sendToBx($url, json_encode($arData));
        $resultProd = json_decode($resultProd, 1);
        return $resultProd;
    }

    public function actionFillField()
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $request = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => 187,
                'order' => ['id' => 'ASC'], // по возрастанию
                'filter' => [
                    'categoryId' => 14,
                    '>id' => 0,
                    'ufCrm8Idinproduction' => ''
                ]
            ]
        );
        $count = ArrayHelper::getValue($request, 'total');
        while ($count > 0) {
            $maxId = max(ArrayHelper::getColumn($request['result']['items'], 'id'));
            $component = new \wm\b24tools\b24Tools();
            $b24App = $component->connectFromAdmin();
            $obB24 = new \Bitrix24\B24Object($b24App);
            foreach ($request['result']['items'] as $item) {
                $obB24->client->addBatchCall(
                    'crm.item.update',
                    [
                        'entityTypeId' => 187,
                        'id' => $item['id'],
                        'fields' => [
                            'ufCrm8Idinproduction' => $item['id'] // ID в воронке Производство
                        ]
                    ]
                );
            }
            $obB24->client->processBatchCalls();
            sleep(5);
            $component = new \wm\b24tools\b24Tools();
            $b24App = $component->connectFromAdmin();
            $obB24 = new \Bitrix24\B24Object($b24App);
            $request = $obB24->client->call(
                'crm.item.list',
                [
                    'entityTypeId' => 187,
                    'order' => ['id' => 'ASC'], // по возрастанию
                    'filter' => [
                        'categoryId' => 14,
                        '>id' => $maxId,
                        'ufCrm8Idinproduction' => ''
                    ]
                ]
            );
            $count = ArrayHelper::getValue($request, 'total');
        }
        return 'ok';
    }

    public function actionGetEntityByFilter()
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $request = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => 187,
                'order' => ['id' => 'ASC'], // по возрастанию
                'filter' => [
                    'categoryId' => 18,
                    '!=ufCrm8Idinproduction' => ''
                ]
            ]
        );
        return $request;
    }

    public function actionDate_mail()
    {
        $ID = 32764; // id сделки
        $RESPONSIBLE_ID = 29406; // Ирина Бондаренко ASSIGNED_BY_ID
        $arData = ["ID" => 32764];

        //ищем сделку
        $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.deal.get.json';
        $resultDeal = self::sendToBx($url, json_encode($arData));
        $resultDeal = json_decode($resultDeal, 1);

        $resultDateDeal = date('d.m.Y', strtotime($resultDeal['result']["UF_CRM_1731413439286"] . ' -3 days'));

        $IP = ""; // 'ИП: ИП Мелехин Н.С.' или 'ИП: ИП Лобова А.Б. или ""'
        if ($resultDeal['result']['UF_CRM_1624975584152']) {
            $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.deal.fields.json';
            $IPs = self::sendToBx($url);
            $IPs = json_decode($IPs, 1);
            foreach ($IPs['result']['UF_CRM_1624975584152']['items'] as $arrIP) {
                if ($arrIP['ID'] == $resultDeal['result']['UF_CRM_1624975584152']) {
                    $IP = 'ИП: ' . $arrIP['VALUE'];
                }
            }
        }

        // получим данные сотрудника (ответственного)
        $respEmplData = ["FILTER" => ["ID" => $resultDeal['result']["ASSIGNED_BY_ID"]]];

        $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/user.get.json';
        $respEmpl = self::sendToBx($url, json_encode($respEmplData));
        $respEmpl = json_decode($respEmpl, 1);

        $DESCRIPTION_all = '--------------Товарные позиции-------';

        // ищем товары
        $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.deal.productrows.get.json';
        $resultOrder = self::sendToBx($url, json_encode($arData));
        $resultOrder = json_decode($resultOrder, 1);

        $numProd = 0;
        $arr = [];
        $resultDate = [];
        $countProd = [];
        $DESCRIPTION = [];
        $DESCRIPTIONm = [];
        $DESCRIPTION_SIMP = [];
        $DESCRIPTION_SIMPm = [];
        $DESCRIPTION_Sm = [];

        //если в сделке есть товары
        if (!empty($resultOrder["result"])) {
            //получим массив групп на портале
            $arData = ['ORDER' => ['NAME' => 'ASC'],
                'FILTER' => ["ACTIVE" => "Y"],
                'IS_ADMIN' => 'Y'];

            $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/sonet_group.get.json';
            $resultGroup = self::sendToBx($url);
            $resultGroup = json_decode($resultGroup, 1);

            //читаем резалтный массив
            foreach ($resultOrder["result"] as $arrProd) {
                $arData = ["ID" => $arrProd['PRODUCT_ID']];
                // ищем товар
                $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.product.get.json';
                $resultProd = self::sendToBx($url, json_encode($arData));
                $resultProd = json_decode($resultProd, 1);
                // переименуем название товара для поставщика
                if ($resultProd["result"]['PROPERTY_166']["value"] == null) {
                    $nameProd = $arrProd['PRODUCT_NAME'];
                } else {
                    $nameProd = quotemeta($arrProd['ORIGINAL_PRODUCT_NAME']);
                    $nameProd = preg_replace("/" . $nameProd . "/", "", $arrProd['PRODUCT_NAME']);
                    $nameProd = $resultProd["result"]['PROPERTY_166']["value"] . $nameProd;
                }

                if (!empty($resultProd["result"]['PROPERTY_156']["value"])) {
                    $idP = $resultProd["result"]['PROPERTY_156']["value"];
                    // ищем производителя
                    $arData = ["ID" => $idP];
                    $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.company.get.json';
                    $resultComp = self::sendToBx($url, json_encode($arData));
                    $resultComp = json_decode($resultComp, 1);

                    if (!preg_match('/Доставка/', $resultProd["result"]['NAME']) && !preg_match('/Сборка/', $resultProd["result"]['NAME'])) {
                        if (!empty($resultComp['result']["TITLE"])) {

                            foreach ($resultGroup["result"] as $value) {
                                if ($value['NAME'] == $resultComp['result']["TITLE"]) {
                                    $idP = $value['ID'];

                                    $arr[$idP] = $arr[$idP] + 1;
                                    foreach ($resultComp["result"]['EMAIL'] as $mail) {
                                        if ($mail['VALUE_TYPE'] == "WORK") {
                                            $CompMail[$idP] = $mail["VALUE"];

                                        };
                                    };
                                    $Comp[$idP] = $resultComp['result']["TITLE"];
                                    if ($resultDate[$idP] < $resultProd["result"]['PROPERTY_162']["value"]) {
                                        $resultDate[$idP] = $resultProd["result"]['PROPERTY_162']["value"];
                                    };
                                    $countProd[$idP] = $countProd[$idP] + $arrProd["QUANTITY"];
                                    // массив для поставщика
                                    $DESCRIPTION[$idP] = $DESCRIPTION[$idP] . '
    ' . $arr[$idP] . ') ' . $nameProd . ' --- ' . $arrProd["QUANTITY"] . ' шт.';
                                    if ($resultProd["result"]['PROPERTY_248']["value"] || $resultProd["result"]['PROPERTY_250']["value"] || $resultProd["result"]['PROPERTY_252']["value"]) {
                                        $DESCRIPTION[$idP] = $DESCRIPTION[$idP] . '
	Д х Г х В : ' . $resultProd["result"]['PROPERTY_248']["value"] . ' x ' . $resultProd["result"]['PROPERTY_250']["value"] . ' x ' . $resultProd["result"]['PROPERTY_252']["value"] . ' см';
                                    }

                                    // массив для менеджера
                                    $DESCRIPTIONm[$idP] = $DESCRIPTIONm[$idP] . '
    ' . $arr[$idP] . ') ' . $arrProd['PRODUCT_NAME'] . ' --- ' . $arrProd["QUANTITY"] . ' шт.';
                                    if ($resultProd["result"]['PROPERTY_248']["value"] || $resultProd["result"]['PROPERTY_250']["value"] || $resultProd["result"]['PROPERTY_252']["value"]) {
                                        $DESCRIPTIONm[$idP] = $DESCRIPTIONm[$idP] . '
	Д х Г х В : ' . $resultProd["result"]['PROPERTY_248']["value"] . ' x ' . $resultProd["result"]['PROPERTY_250']["value"] . ' x ' . $resultProd["result"]['PROPERTY_252']["value"] . ' см';
                                    }


                                    $verify = $resultComp['result']["TITLE"];
                                }
                            }
                            if ($verify != $resultComp['result']["TITLE"]) {
                                $idP = $resultComp['result']["TITLE"];
                                $arr[$idP] = $arr[$idP] + 1;
                                $countProd[$idP] = $countProd[$idP] + $arrProd["QUANTITY"];
                                $Comp[$idP] = $resultComp['result']["TITLE"];
                                // массив для поставщика
                                $DESCRIPTION_SIMP[$idP] = $DESCRIPTION_SIMP[$idP] . '
    ' . $arr[$idP] . ') ' . $nameProd . ' --- ' . $arrProd["QUANTITY"] . ' шт. <br>';
                                if ($resultProd["result"]['PROPERTY_248']["value"] || $resultProd["result"]['PROPERTY_250']["value"] || $resultProd["result"]['PROPERTY_252']["value"]) {
                                    $DESCRIPTION_SIMP[$idP] = $DESCRIPTION_SIMP[$idP] . '
	Д х Г х В : ' . $resultProd["result"]['PROPERTY_248']["value"] . ' x ' . $resultProd["result"]['PROPERTY_250']["value"] . ' x ' . $resultProd["result"]['PROPERTY_252']["value"] . ' см';
                                }
                                // массив для менеджера
                                $DESCRIPTION_SIMPm[$idP] = $DESCRIPTION_SIMPm[$idP] . '
    ' . $arr[$idP] . ') ' . $arrProd['PRODUCT_NAME'] . ' --- ' . $arrProd["QUANTITY"] . ' шт.';
                                if ($resultProd["result"]['PROPERTY_248']["value"] || $resultProd["result"]['PROPERTY_250']["value"] || $resultProd["result"]['PROPERTY_252']["value"]) {
                                    $DESCRIPTION_SIMPm[$idP] = $DESCRIPTION_SIMPm[$idP] . '
	Д х Г х В : ' . $resultProd["result"]['PROPERTY_248']["value"] . ' x ' . $resultProd["result"]['PROPERTY_250']["value"] . ' x ' . $resultProd["result"]['PROPERTY_252']["value"] . ' см';
                                }

                                foreach ($resultComp["result"]['EMAIL'] as $mail) {
                                    if ($mail['VALUE_TYPE'] == "WORK") {
                                        $CompMail[$idP] = $mail["VALUE"];
                                    };
                                };
                                if ($resultDate[$idP] < $resultProd["result"]['PROPERTY_162']["value"]) {
                                    $resultDate[$idP] = $resultProd["result"]['PROPERTY_162']["value"];
                                };
                            };

                        } else {
                            $idP = 'NO_PROD';
                            $arr[$idP] = $arr[$idP] + 1;
                            $countProd[$idP] = $countProd[$idP] + $arrProd["QUANTITY"];
                            $DESCRIPTION_Sm[$idP] = $DESCRIPTION_Sm[$idP] . '
		' . $arr[$idP] . ') ' . $arrProd['PRODUCT_NAME'] . ' --- ' . $arrProd["QUANTITY"] . ' шт.';
                            if ($resultProd["result"]['PROPERTY_248']["value"] || $resultProd["result"]['PROPERTY_250']["value"] || $resultProd["result"]['PROPERTY_252']["value"]) {
                                $DESCRIPTION_Sm[$idP] = $DESCRIPTION_Sm[$idP] . '
		Д х Г х В : ' . $resultProd["result"]['PROPERTY_248']["value"] . ' x ' . $resultProd["result"]['PROPERTY_250']["value"] . ' x ' . $resultProd["result"]['PROPERTY_252']["value"] . ' см';
                            }

                            if ($resultDate[$idP] < $resultProd["result"]['PROPERTY_162']["value"]) {
                                $resultDate[$idP] = $resultProd["result"]['PROPERTY_162']["value"];
                            }

                        }
                    }
                }

            }
            $titleTask = count($DESCRIPTION) + count($DESCRIPTION_SIMP) + count($DESCRIPTION_Sm);
            if ($titleTask > 1) {
                $titleTask = $ID . ' + (' . $titleTask . ')';
            } else {
                $titleTask = $ID;
            }

            //обработка товаров экстранетов
            if (!empty($DESCRIPTION)) {

                /*
                $DESCRIPTION = {
                    "50": "\r\n    1) Кровать Базз с отсеком 160 x 200Кровать Базз с отсеком 160 x 200. Ткань 5 кат.
                [Art Vision Paradise 13]. Габариты 172 × 210 × 35 см. Скругленные углы на раме.
                --- 1 шт.\r\n\tД х Г х В : 172 x 210 x 35 см\r\n    2) Диван Кермит 180 x 105Диван Кермит 175 x 105.
                Ткань 5 кат. [Art Vision Paradise 13]. Две секции. Две декоративные подушки в комплекте.
                Слева с подлокотником, справа без подлокотника ( смотрим на диван). Подлокотник 20 см.
                Встроить механизм Пантограф.  --- 1 шт.\r\n\tД х Г х В : 180 x 105 x 75 см"
                            }
                */

                //создаем письмо из задачи
                foreach ($DESCRIPTION as $key => $product) {

                    /*
                    $resultDate = { "50": "25"}
                    */

                    $resultDate[$key] = date('d.m.Y H:i', strtotime(date('d.m.Y H:i') . $resultDate[$key] . ' day'));

                    /*
                    $resultDate = { "50": "08.12.2024 06:37"} // Сегодня + 25 дней
                    */

                    $bodyMail = $IP . '  

' . $product . '<br>
Всего наименований: ' . $arr[$key] . ', общее количество изделий: ' . $countProd[$key] . ' шт.' . '<br><br>

!!! Крайний срок реализации: ' . $resultDateDeal . '
<br><br><br>

--
<br><br>
С уважением, ' . $respEmpl['result'][0]['NAME'] . ' ' . $respEmpl['result'][0]['LAST_NAME'] .
                        '<br>Тел / whatsapp ' . $respEmpl['result'][0]['WORK_PHONE'];


                    $product = $IP . '
			
Товарные позиции:
' . $product . '

Всего наименований: ' . $arr[$key] . ', общее количество изделий: ' . $countProd[$key] . ' шт.';


                    $productM = '
			
Поставщик ' . $Comp[$key] . '
Срок реализации: ' . $resultDateDeal . '
' . $IP . '

Товарные позиции:' . $DESCRIPTIONm[$key] . '

Всего наименований: ' . $arr[$key] . ', общее количество изделий: ' . $countProd[$key] . ' шт.';


                    //ищем задачу поставщика
                    $arData = ["filter" => ["TITLE" => $ID . ' / ' . $Comp[$key]],
                        "select" => ["ID", "TITLE", "DESCRIPTION"],
                        "order" => ["ID" => "asc"]];

                    $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/tasks.task.list.json';
                    $result = self::sendToBx($url, json_encode($arData));
                    $result = json_decode($result, 1);
                    $countProd['ext'] = $countProd['ext'] + $countProd[$key];
                    $numProd = $numProd + 1;

                    if (!empty($result["result"]["tasks"][0])) {
                        //отправка письма поставщику
                        $bodyMail = str_replace(array("\r\n", "\r", "\n"), "<br>", $result["result"]["tasks"][0]['description']);
                        $bodyMail = $bodyMail . '<br><br>
--
<br><br>
С уважением, ' . $respEmpl['result'][0]['NAME'] . ' ' . $respEmpl['result'][0]['LAST_NAME'] .
                            '<br>Тел / whatsapp ' . $respEmpl['result'][0]['WORK_PHONE'];

                        $arData = [
                            'fields' => [
                                "SUBJECT" => "Заказ " . $ID,
                                "DESCRIPTION" => $bodyMail,
                                "DESCRIPTION_TYPE" => 3,//text,html,bbCode type id in: CRest::call('crm.enum.contenttype');
                                "COMPLETED" => "N",//send now Y
                                "DIRECTION" => 2,
                                "OWNER_ID" => $ID,
                                "OWNER_TYPE_ID" => 2,
                                "TYPE_ID" => 4,
                                "COMMUNICATIONS" => [
                                    [
                                        'VALUE' => $CompMail[$key],
                                        'ENTITY_TYPE_ID' => 3
                                    ]
                                ],
                                "START_TIME" => date("Y-m-d H:i:s", time()),
                                "END_TIME" => date("Y-m-d H:i:s", time() + 3600),
                                "RESPONSIBLE_ID" => $resultDeal['result']["ASSIGNED_BY_ID"],
                                'SETTINGS' => [
                                    'MESSAGE_FROM' => "LAVSIT <service@lavsit.ru>",

                                ],
                            ]
                        ];

                        $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.activity.add.json';
//                        $resultMess = sendToBx($url, json_encode($arData));

                        $DESCRIPTION_all = $DESCRIPTION_all . $numProd . '. ' . $productM . '
						ссылка на задачу: https://lavsit.bitrix24.ru/workgroups/group/' . $key . '/tasks/task/view/' . $result["result"]["tasks"][0]['id'] . '/ 
						';
                        $DESCRIPTION_all = $DESCRIPTION_all . '
					
					------------------------------------------------------------------------------------------------------------------------------------
						
						';

                    } else {
                        //ищем ответственнного среди экстранета по почте!!!
                        $arData = ['FILTER' => ['USER_TYPE' => "extranet"]];

                        $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/user.search.json';
                        $result = self::sendToBx($url, json_encode($arData));
                        $result = json_decode($result, 1);

                        $RESPONSIBLE = $RESPONSIBLE_ID;
                        foreach ($result['result'] as $respUs) {
                            if ($respUs['EMAIL'] == $CompMail[$key]) {
                                $RESPONSIBLE = $respUs["ID"];
                            }
                        }

                        //создаем задачу поставщику
                        $arData = ['fields' => ['TITLE' => $ID . ' / ' . $Comp[$key],
                            'UF_CRM_TASK' => ["D_" . $ID],
                            'GROUP_ID' => $key,
                            "DESCRIPTION" => $product,
                            "DEADLINE" => $resultDateDeal,
                            "RESPONSIBLE_ID" => $RESPONSIBLE,
                            "CREATED_BY" => $resultDeal['result']["ASSIGNED_BY_ID"]
                        ]
                        ];


                        /*
                        $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/tasks.task.add.json';
                        $result = sendToBx($url, json_encode($arData));
                        $result = json_decode($result, 1);

                        $DESCRIPTION_all = $DESCRIPTION_all . $numProd . '. ' . $productM . '
	ссылка на задачу: https://lavsit.bitrix24.ru/workgroups/group/' . $key . '/tasks/task/view/' . $result['result']['task']['id'] . '/ 
	';
                        $DESCRIPTION_all = $DESCRIPTION_all . '

------------------------------------------------------------------------------------------------------------------------------------
	
	';

                        */


                        //отправка письма поставщику
                        $arData = [
                            'fields' => [
                                "SUBJECT" => "Заказ " . $ID,
                                "DESCRIPTION" => $bodyMail,
                                "DESCRIPTION_TYPE" => 3,//text,html,bbCode type id in: CRest::call('crm.enum.contenttype');
                                "COMPLETED" => "N",//send now Y
                                "DIRECTION" => 2,
                                "OWNER_ID" => $ID,
                                "OWNER_TYPE_ID" => 2,
                                "TYPE_ID" => 4,
                                "COMMUNICATIONS" => [
                                    [
                                        'VALUE' => $CompMail[$key],
                                        'ENTITY_TYPE_ID' => 3
                                    ]
                                ],
                                "START_TIME" => date("Y-m-d H:i:s", time()),
                                "END_TIME" => date("Y-m-d H:i:s", time() + 3600),
                                "RESPONSIBLE_ID" => $resultDeal['result']["ASSIGNED_BY_ID"],
                                'SETTINGS' => [
                                    'MESSAGE_FROM' => "LAVSIT <service@lavsit.ru>",

                                ],
                            ]
                        ];

                        $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.activity.add.json';
//                        $result = sendToBx($url, json_encode($arData));

                    }

                    if ($dateResD < $resultDate[$key]) {
                        $dateResD = $resultDate[$key];
                    }

                }

            }

            //обработка товаров НЕ экстранетов
            if (!empty($DESCRIPTION_SIMP)) {
                foreach ($DESCRIPTION_SIMP as $key => $products) {
                    $resultDate[$key] = date('d.m.Y H:i', strtotime(date('d.m.Y H:i') . $resultDate[$key] . ' day'));

                    $bodyMail = $IP . ' 

 ' . $products . '<br><br>

Всего наименований: ' . $arr[$key] . ', общее количество изделий: ' . $countProd[$key] . ' шт.<br><br>

!!! Крайний срок реализации: ' . $resultDateDeal . '
<br><br><br>

--
<br><br>
С уважением, ' . $respEmpl['result'][0]['NAME'] . ' ' . $respEmpl['result'][0]['LAST_NAME'] .
                        '<br>Тел / whatsapp ' . $respEmpl['result'][0]['WORK_PHONE'];

                    $productsM = 'Поставщик ' . $Comp[$key] . '
Срок реализации: ' . $resultDateDeal . '

' . $IP . '

Товарные позиции:' . $DESCRIPTION_SIMPm[$key] . '
Всего наименований: ' . $arr[$key] . ', общее количество изделий: ' . $countProd[$key] . ' шт.';

                    //отправляем письмо поставщику
                    $arData = [
                        'fields' => [
                            "SUBJECT" => "Заказ " . $ID,
                            "DESCRIPTION" => $bodyMail,
                            "DESCRIPTION_TYPE" => 3,//text,html,bbCode type id in: CRest::call('crm.enum.contenttype');
                            "COMPLETED" => "N",//send now Y
                            "DIRECTION" => 2,
                            "OWNER_ID" => $ID,
                            "OWNER_TYPE_ID" => 2,
                            "TYPE_ID" => 4,
                            "COMMUNICATIONS" => [
                                [
                                    'VALUE' => $CompMail[$key],
                                    'ENTITY_TYPE_ID' => 3
                                ]
                            ],
                            "START_TIME" => date("Y-m-d H:i:s", time()),
                            "END_TIME" => date("Y-m-d H:i:s", time() + 3600),
                            "RESPONSIBLE_ID" => $resultDeal['result']["ASSIGNED_BY_ID"],
                            'SETTINGS' => [
                                'MESSAGE_FROM' => "LAVSIT <service@lavsit.ru>",

                            ],
                        ]
                    ];

                    $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.activity.add.json';
//                    $result = sendToBx($url, json_encode($arData));

                    $countProd['ext'] = $countProd['ext'] + $countProd[$key];
                    $numProd = $numProd + 1;
                    $DESCRIPTION_all = $DESCRIPTION_all . $numProd . '. ' . $productsM . '
';
                    $DESCRIPTION_all = $DESCRIPTION_all . '
------------------------------------------------------------------------------------------------------------------------------------

';
                };

            }

            //обработка товаров без поставщика
            if (!empty($DESCRIPTION_Sm)) {
                $countProd['ext'] = $countProd['ext'] + $countProd['NO_PROD'];
                if (!empty($resultDeal['result']["UF_CRM_1616420219472"])) {
                    //ищем задачу поставщика
                    $arData = ["taskId" => $resultDeal['result']["UF_CRM_1616420219472"]];

                    $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/tasks.task.get.json';
                    $resultTask = sendToBx($url, json_encode($arData));
                    $resultTask = json_decode($resultTask, 1);

                    foreach ($resultGroup["result"] as $value) {
                        if ($value["ID"] == $resultTask["result"]["task"]["groupId"]) {    // ищем производителя
                            $arData = ["filter" => ["TITLE" => $value["NAME"]],
                                "select" => ["ID", "TITLE", "EMAIL"],
                                "order" => ["ID" => "asc"]];

                            $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.company.list.json';
                            $resultComp = sendToBx($url, json_encode($arData));
                            $resultComp = json_decode($resultComp, 1);

                            foreach ($resultComp["result"][0]['EMAIL'] as $mail) {
                                if ($mail['VALUE_TYPE'] == "WORK") {
                                    $bodyMail = str_replace(array("\r\n", "\r", "\n"), "<br>", $resultTask["result"]["task"]['description']);
                                    $bodyMail = str_replace("Распределить товары по поставщикам, сформировав задачи по группе товаров для каждого из них, <br>	и добавить в \"Задача в проекте (группе)\" этого поставщика", "", $bodyMail);
                                    //отправляем письмо поставщику
                                    $arData = [
                                        'fields' => [
                                            "SUBJECT" => "Заказ " . $ID,
                                            "DESCRIPTION" => $bodyMail . '
											--
											<br><br>
											С уважением, ' . $respEmpl['result'][0]['NAME'] . ' ' . $respEmpl['result'][0]['LAST_NAME'] .
                                                '<br>Тел / whatsapp ' . $respEmpl['result'][0]['WORK_PHONE'],
                                            "DESCRIPTION_TYPE" => 3,//text,html,bbCode type id in: CRest::call('crm.enum.contenttype');
                                            "COMPLETED" => "N",//send now Y
                                            "DIRECTION" => 2,
                                            "OWNER_ID" => $ID,
                                            "OWNER_TYPE_ID" => 2,
                                            "TYPE_ID" => 4,
                                            "COMMUNICATIONS" => [
                                                [
                                                    'VALUE' => $mail["VALUE"],
                                                    'ENTITY_TYPE_ID' => 3
                                                ]
                                            ],
                                            "START_TIME" => date("Y-m-d H:i:s", time()),
                                            "END_TIME" => date("Y-m-d H:i:s", time() + 3600),
                                            "RESPONSIBLE_ID" => $resultDeal['result']["ASSIGNED_BY_ID"],
                                            'SETTINGS' => [
                                                'MESSAGE_FROM' => "LAVSIT <service@lavsit.ru>",

                                            ],
                                        ]
                                    ];

                                    $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.activity.add.json';
//                            $result = sendToBx($url, json_encode($arData));
                                };
                            };
                        }
                    }

                    $DESCR = '
	Товары без предопределённого поставщика 
	
' . $IP . ' 

Товарные позиции:' . $DESCRIPTION_Sm['NO_PROD'] . '
	Всего наименований: ' . $arr['NO_PROD'] . ', общее количество изделий: ' . $countProd['NO_PROD'] . ' шт.

	ссылка на задачу: https://lavsit.bitrix24.ru/workgroups/group/' . $resultTask["result"]["task"]["groupId"] . '/tasks/task/view/' . $resultTask['result']['task']['id'] . '/ 
	';
                } else {
                    $DESCR = '
	Распределить товары по поставщикам, сформировав задачи по группе товаров для каждого из них, 
	и добавить в "Задача в проекте (группе)" этого поставщика

	' . $IP . '

	Товарные позиции:' . $DESCRIPTION_Sm['NO_PROD'] . '
	Всего наименований: ' . $arr['NO_PROD'] . ', общее количество изделий: ' . $countProd['NO_PROD'] . ' шт.';
                }

            }

            //создаем задачу менеджеру
            $DESCR = $DESCRIPTION_all . $DESCR .
                '

Общее количество товаров: ' . $countProd['ext'];

            $arData = ['fields' => ['TITLE' => $titleTask,
                'UF_CRM_TASK' => ["D_" . $ID],
                'GROUP_ID' => '14',
                "DESCRIPTION" => $DESCR,
                "DEADLINE" => $resultDateDeal,
                "RESPONSIBLE_ID" => $resultDeal['result']["ASSIGNED_BY_ID"],
                "CREATED_BY" => $resultDeal['result']["ASSIGNED_BY_ID"]
            ]
            ];

            $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/tasks.task.add.json';
//            $result= sendToBx($url, json_encode($arData));

        }
        return $resultDate;
    }

    public static function sendToBx($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
