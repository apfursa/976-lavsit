<?php

namespace app\controllers\test;

use app\models\ReportCompanySearch;
use wm\yii\rest\ActiveRestController;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\helpers\Url;
use Yii;

class Mail2Controller extends Controller
{
    // https://webmens3.bitrix24.ru/devops/edit/out-hook/20/
    public function actionTest()
    {
        // токен приложения
        // 7vd8xl0gtli35otjyokci2864ew6cd5h
        $request = Yii::$app->request;

        /*
        [
            'enableCsrfValidation' => true,
            'csrfParam' => '_csrf',
            'csrfCookie' => [
                'httpOnly' => true,
            ],
            'enableCsrfCookie' => true,
            'enableCookieValidation' => true,
            'cookieValidationKey' => 'wH1OEMwmya_gLoPVLOTtp5QtHaMuhKfR',
            'methodParam' => '_method',
            'parsers' => [
                'application/json' => 'yii\\web\\JsonParser',
            ],
            'trustedHosts' => [],
            'secureHeaders' => [
                'X-Forwarded-For',
                'X-Forwarded-Host',
                'X-Forwarded-Proto',
                'X-Forwarded-Port',
                'Front-End-Https',
                'X-Rewrite-Url',
                'X-Original-Host',
            ],
            'ipHeaders' => [
                'X-Forwarded-For',
            ],
            'portHeaders' => [
                'X-Forwarded-Port',
            ],
            'secureProtocolHeaders' => [
                'X-Forwarded-Proto' => [
                    'https',
                ],
                'Front-End-Https' => [
                    'on',
                ],
            ],
        ]
            */
        Yii::warning($request->post(), 'Webhook_$request');

        /*
        [
            'event' => 'ONCRMDEALADD',
            'event_id' => '80',
            'data' => [
                'FIELDS' => [
                    'ID' => '116',
                ],
            ],
            'ts' => '1720505868',
            'auth' => [
                'domain' => 'webmens3.bitrix24.ru',
                'client_endpoint' => 'https://webmens3.bitrix24.ru/rest/',
                'server_endpoint' => 'https://oauth.bitrix.info/rest/',
                'member_id' => 'f3b1042cf2591ec2fb69d3b75a6b8964',
                'application_token' => '7vd8xl0gtli35otjyokci2864ew6cd5h',
            ],
        ]
            */
        if ($request->post()['auth']['application_token'] == '7vd8xl0gtli35otjyokci2864ew6cd5h') {
            Yii::warning('yes', 'request');
            return 'yes';
        }
        Yii::warning('no', 'request');
        return 'no';
    }


    // https://webmens3.bitrix24.ru/devops/edit/in-hook/22/
    public function actionTest2()
    {
        // токен приложения
        // https://webmens3.bitrix24.ru/rest/6/4ayywzc071jk3p20/

        /*токен который выдаётся при регистрации бота */
        $token = "4ayywzc071jk3p20";
        $method = "crm.deal.delete";
        $userId = '6';
        $arrayQuery = [
            "userId" => $userId,
        ];
//        $ch = curl_init('https://webmens3.bitrix24.ru/rest/' . $userId . '/' . $token . '/' . $method . '?id=118');
        $ch = curl_init('https://webmens3.bitrix24.ru/rest/6/s99zay0e14yfmi0s/crm.deal.get?id=116');
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res);
    }

    public function actionMail()
    {

        // https://sof.lavsit.ru/test/mail2/mail
        // Токен приложения
        // j3dqrzndiwhx2y0eu5lp1nlcip0f9f80

        $log_file = 'mail.txt';
        file_put_contents($log_file, date('y-m-d H:i:s') . " !!! Входные данные вебхук тест: " . json_encode($_REQUEST, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

        $ID = $_REQUEST['ID'];
        $RESPONSIBLE_ID = $_REQUEST['RESPONSIBLE_ID'];
        $arData = ["ID" => $ID];

//        $ID = 32764; // id сделки
//        $RESPONSIBLE_ID = 29406; // Ирина Бондаренко ASSIGNED_BY_ID
//        $arData = ["ID" => 32764];

        //ищем сделку
        $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.deal.get.json';
        $resultDeal = self::sendToBx($url, json_encode($arData));
        file_put_contents($log_file, "    ищем сделку: " . $resultDeal . str_repeat("\r\n", 2), FILE_APPEND);
        $resultDeal = json_decode($resultDeal, 1);

        $resultDateDeal = date('d.m.Y', strtotime($resultDeal['result']["UF_CRM_1731413439286"] . ' -3 days'));
        file_put_contents($log_file, "    дата завершения_resultDateDeal: " . $resultDateDeal . str_repeat("\r\n", 2), FILE_APPEND);

        //ищем ИП
        $IP = ""; // 'ИП: ИП Мелехин Н.С.' или 'ИП: ИП Лобова А.Б. или ""'
        if ($resultDeal['result']['UF_CRM_1624975584152']) {
            $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.deal.fields.json';
            $IPs = self::sendToBx($url);
            file_put_contents($log_file, "    ищем ИП: " . $IPs . str_repeat("\r\n", 2), FILE_APPEND);
            $IPs = json_decode($IPs, 1);
            foreach ($IPs['result']['UF_CRM_1624975584152']['items'] as $arrIP) {
                if ($arrIP['ID'] == $resultDeal['result']['UF_CRM_1624975584152']) {
                    $IP = 'ИП: ' . $arrIP['VALUE'];
                }
            }
        }
        file_put_contents($log_file, "    IP: " . $IP . str_repeat("\r\n", 2), FILE_APPEND);

        // получим данные сотрудника (ответственного)
        $respEmplData = ["FILTER" => ["ID" => $resultDeal['result']["ASSIGNED_BY_ID"]]];

        $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/user.get.json';
        $respEmpl = self::sendToBx($url, json_encode($respEmplData));
        file_put_contents($log_file, "    данные ответственного: " . $respEmpl . str_repeat("\r\n", 2), FILE_APPEND);
        $respEmpl = json_decode($respEmpl, 1);

        $DESCRIPTION_all = '--------------Товарные позиции-------';

        // ищем товары
        $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.deal.productrows.get.json';
        $resultOrder = self::sendToBx($url, json_encode($arData));
        file_put_contents($log_file, "    ищем товары: " . $resultOrder . str_repeat("\r\n", 2), FILE_APPEND);
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
                'IS_ADMIN' => 'Y'
            ];

            $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/sonet_group.get.json';
            $resultGroup = self::sendToBx($url);
            file_put_contents($log_file, "    массив групп: " . $resultGroup . str_repeat("\r\n", 2), FILE_APPEND);
            $resultGroup = json_decode($resultGroup, 1);

            //читаем резалтный массив
            foreach ($resultOrder["result"] as $arrProd) {
                $arData = ["ID" => $arrProd['PRODUCT_ID']];
                // ищем товар
                $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.product.get.json';
                $resultProd = self::sendToBx($url, json_encode($arData));
                file_put_contents($log_file, "    ищем товар: " . $resultProd . str_repeat("\r\n", 2), FILE_APPEND);
                $resultProd = json_decode($resultProd, 1);
                // переименуем название товара для поставщика
                if ($resultProd["result"]['PROPERTY_166']["value"] == null) {
                    $nameProd = $arrProd['PRODUCT_NAME'];
                    file_put_contents($log_file, "    переименуем название товара для поставщика: " . $nameProd . str_repeat("\r\n", 2), FILE_APPEND);
                } else {
                    $nameProd = quotemeta($arrProd['ORIGINAL_PRODUCT_NAME']);
                    $nameProd = preg_replace("/" . $nameProd . "/", "", $arrProd['PRODUCT_NAME']);
                    file_put_contents($log_file, "    переименуем название товара для поставщика else: " . $nameProd . str_repeat("\r\n", 2), FILE_APPEND);
                    $nameProd = $resultProd["result"]['PROPERTY_166']["value"] . $nameProd;
                    file_put_contents($log_file, "nameProd : " . $nameProd . str_repeat("\r\n", 2), FILE_APPEND);
                }

                if (!empty($resultProd["result"]['PROPERTY_156']["value"])) {
                    $idP = $resultProd["result"]['PROPERTY_156']["value"];
                    // ищем производителя
                    $arData = ["ID" => $idP];
                    $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.company.get.json';
                    $resultComp = self::sendToBx($url, json_encode($arData));
                    file_put_contents($log_file, "    ищем производителя: " . $resultComp . str_repeat("\r\n", 2), FILE_APPEND);
                    $resultComp = json_decode($resultComp, 1);

                    if (!preg_match('/Доставка/', $resultProd["result"]['NAME']) && !preg_match('/Сборка/', $resultProd["result"]['NAME'])) {
                        file_put_contents($log_file, "    preg_match: " . str_repeat("\r\n", 2), FILE_APPEND);
                        if (!empty($resultComp['result']["TITLE"])) {
                            file_put_contents($log_file, "    empty: " . str_repeat("\r\n", 2), FILE_APPEND);

                            foreach ($resultGroup["result"] as $value) {
                                if ($value['NAME'] == $resultComp['result']["TITLE"]) {
                                    file_put_contents($log_file, "    value_NAME: " . json_encode($value, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
                                    file_put_contents($log_file, "    resultComp: " . json_encode($resultComp, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
                                    $idP = $value['ID'];

                                    $arr[$idP] = $arr[$idP] + 1;
                                    file_put_contents($log_file, "    arr: " . json_encode($arr, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
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
                                    file_put_contents($log_file, "    массив для поставщика DESCRIPTION: " . json_encode($DESCRIPTION, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

                                    // массив для менеджера
                                    $DESCRIPTIONm[$idP] = $DESCRIPTIONm[$idP] . '
    ' . $arr[$idP] . ') ' . $arrProd['PRODUCT_NAME'] . ' --- ' . $arrProd["QUANTITY"] . ' шт.';
                                    if ($resultProd["result"]['PROPERTY_248']["value"] || $resultProd["result"]['PROPERTY_250']["value"] || $resultProd["result"]['PROPERTY_252']["value"]) {
                                        $DESCRIPTIONm[$idP] = $DESCRIPTIONm[$idP] . '
	Д х Г х В : ' . $resultProd["result"]['PROPERTY_248']["value"] . ' x ' . $resultProd["result"]['PROPERTY_250']["value"] . ' x ' . $resultProd["result"]['PROPERTY_252']["value"] . ' см';
                                    }
                                    file_put_contents($log_file, "    массив для менеджера DESCRIPTIONm: " . json_encode($DESCRIPTIONm, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);


                                    $verify = $resultComp['result']["TITLE"];
                                    file_put_contents($log_file, "    verify: " . json_encode($verify, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
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
                                file_put_contents($log_file, "    массив для поставщика_2 DESCRIPTION_SIMP: " . json_encode($DESCRIPTION_SIMP, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);


                                // массив для менеджера
                                $DESCRIPTION_SIMPm[$idP] = $DESCRIPTION_SIMPm[$idP] . '
    ' . $arr[$idP] . ') ' . $arrProd['PRODUCT_NAME'] . ' --- ' . $arrProd["QUANTITY"] . ' шт.';
                                if ($resultProd["result"]['PROPERTY_248']["value"] || $resultProd["result"]['PROPERTY_250']["value"] || $resultProd["result"]['PROPERTY_252']["value"]) {
                                    $DESCRIPTION_SIMPm[$idP] = $DESCRIPTION_SIMPm[$idP] . '
	Д х Г х В : ' . $resultProd["result"]['PROPERTY_248']["value"] . ' x ' . $resultProd["result"]['PROPERTY_250']["value"] . ' x ' . $resultProd["result"]['PROPERTY_252']["value"] . ' см';
                                }

                                file_put_contents($log_file, "    массив для менеджера_2 DESCRIPTION_SIMPm: " . json_encode($DESCRIPTION_SIMPm, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

                                foreach ($resultComp["result"]['EMAIL'] as $mail) {
                                    if ($mail['VALUE_TYPE'] == "WORK") {
                                        $CompMail[$idP] = $mail["VALUE"];
                                    };
                                };
                                if ($resultDate[$idP] < $resultProd["result"]['PROPERTY_162']["value"]) {
                                    $resultDate[$idP] = $resultProd["result"]['PROPERTY_162']["value"];
                                    file_put_contents($log_file, "    resultDate: " . json_encode($resultDate, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
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
                            file_put_contents($log_file, "    resultDate else: " . json_encode($resultDate, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

                            if ($resultDate[$idP] < $resultProd["result"]['PROPERTY_162']["value"]) {
                                $resultDate[$idP] = $resultProd["result"]['PROPERTY_162']["value"];
                                file_put_contents($log_file, "    resultDate < resultProd: " . json_encode($resultProd, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
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
            file_put_contents($log_file, "    titleTask: " . $titleTask . str_repeat("\r\n", 2), FILE_APPEND);

            //обработка товаров экстранетов
            if (!empty($DESCRIPTION)) {

                file_put_contents($log_file, "    DESCRIPTION: " . json_encode($DESCRIPTION, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

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

                    file_put_contents($log_file, "    resultDate: " . json_encode($resultDate, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
                    $bodyMail = $IP . '  

' . $product . '<br>
Всего наименований: ' . $arr[$key] . ', общее количество изделий: ' . $countProd[$key] . ' шт.' . '<br><br>

!!! Крайний срок реализации: ' . $resultDateDeal . '
<br><br><br>

--
<br><br>
С уважением, ' . $respEmpl['result'][0]['NAME'] . ' ' . $respEmpl['result'][0]['LAST_NAME'] .
                        '<br>Тел / whatsapp ' . $respEmpl['result'][0]['WORK_PHONE'];

                    file_put_contents($log_file, "    bodyMail: " . $bodyMail . str_repeat("\r\n", 2), FILE_APPEND);


                    $product = $IP . '
			
Товарные позиции:
' . $product . '

Всего наименований: ' . $arr[$key] . ', общее количество изделий: ' . $countProd[$key] . ' шт.';
                    file_put_contents($log_file, "    product: " . $product . str_repeat("\r\n", 2), FILE_APPEND);


                    $productM = '
			
Поставщик ' . $Comp[$key] . '
Срок реализации: ' . $resultDateDeal . '
' . $IP . '

Товарные позиции:' . $DESCRIPTIONm[$key] . '

Всего наименований: ' . $arr[$key] . ', общее количество изделий: ' . $countProd[$key] . ' шт.';

                    file_put_contents($log_file, "    productM: " . $productM . str_repeat("\r\n", 2), FILE_APPEND);


                    //ищем задачу поставщика
                    $arData = ["filter" => ["TITLE" => $ID . ' / ' . $Comp[$key]],
                        "select" => ["ID", "TITLE", "DESCRIPTION"],
                        "order" => ["ID" => "asc"]];

                    $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/tasks.task.list.json';
                    $result = self::sendToBx($url, json_encode($arData));
                    file_put_contents($log_file, "   ищем задачу поставщика: " . $result . str_repeat("\r\n", 2), FILE_APPEND);
                    $result = json_decode($result, 1);
                    $countProd['ext'] = $countProd['ext'] + $countProd[$key];
                    file_put_contents($log_file, "   countProd: " . json_encode($countProd, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
                    $numProd = $numProd + 1;
                    file_put_contents($log_file, "   numProd: " . json_encode($numProd, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

                    if (!empty($result["result"]["tasks"][0])) {
                        //отправка письма поставщику
                        $bodyMail = str_replace(array("\r\n", "\r", "\n"), "<br>", $result["result"]["tasks"][0]['description']);
                        file_put_contents($log_file, "   bodyMail: " . $bodyMail . str_repeat("\r\n", 2), FILE_APPEND);
                        $bodyMail = $bodyMail . '<br><br>
--
<br><br>
С уважением, ' . $respEmpl['result'][0]['NAME'] . ' ' . $respEmpl['result'][0]['LAST_NAME'] .
                            '<br>Тел / whatsapp ' . $respEmpl['result'][0]['WORK_PHONE'];

                        file_put_contents($log_file, "   bodyMail_2: " . $bodyMail . str_repeat("\r\n", 2), FILE_APPEND);
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
                        file_put_contents($log_file, "   arData: " . json_encode($arData, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

                        $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.activity.add.json';
//                        $resultMess = sendToBx($url, json_encode($arData));

                        $DESCRIPTION_all = $DESCRIPTION_all . $numProd . '. ' . $productM . '
						ссылка на задачу: https://lavsit.bitrix24.ru/workgroups/group/' . $key . '/tasks/task/view/' . $result["result"]["tasks"][0]['id'] . '/ 
						';
                        $DESCRIPTION_all = $DESCRIPTION_all . '
					
					------------------------------------------------------------------------------------------------------------------------------------
						
						';
                        file_put_contents($log_file, "   DESCRIPTION_all: " . $DESCRIPTION_all . str_repeat("\r\n", 2), FILE_APPEND);

                    } else {
                        //ищем ответственнного среди экстранета по почте!!!
                        $arData = ['FILTER' => ['USER_TYPE' => "extranet"]];

                        $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/user.search.json';
                        $result = self::sendToBx($url, json_encode($arData));
                        file_put_contents($log_file, "   ищем ответственнного среди экстранета по почте!!!: " . $result . str_repeat("\r\n", 2), FILE_APPEND);
                        $result = json_decode($result, 1);

                        $RESPONSIBLE = $RESPONSIBLE_ID;
                        foreach ($result['result'] as $respUs) {
                            if ($respUs['EMAIL'] == $CompMail[$key]) {
                                $RESPONSIBLE = $respUs["ID"];
                            }
                        }

                        file_put_contents($log_file, "   RESPONSIBLE: " . $RESPONSIBLE . str_repeat("\r\n", 2), FILE_APPEND);
                        //создаем задачу поставщику
                        $arData = [
                            'fields' => ['TITLE' => $ID . ' / ' . $Comp[$key],
                                'UF_CRM_TASK' => ["D_" . $ID],
                                'GROUP_ID' => $key,
                                "DESCRIPTION" => $product,
                                "DEADLINE" => $resultDateDeal,
                                "RESPONSIBLE_ID" => $RESPONSIBLE,
                                "CREATED_BY" => $resultDeal['result']["ASSIGNED_BY_ID"]
                            ]
                        ];

                        file_put_contents($log_file, "   создаем задачу поставщику: " . json_encode($arData, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

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

                        file_put_contents($log_file, "   отправка письма поставщику: " . json_encode($arData, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

                        $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.activity.add.json';
//                        $result = sendToBx($url, json_encode($arData));

                    }

                    if ($dateResD < $resultDate[$key]) {
                        $dateResD = $resultDate[$key];
                    }

                }

            }

            file_put_contents($log_file, "   DESCRIPTION_SIMP: " . json_encode($DESCRIPTION_SIMP, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
            //обработка товаров НЕ экстранетов
            if (!empty($DESCRIPTION_SIMP)) {
                foreach ($DESCRIPTION_SIMP as $key => $products) {
                    $resultDate[$key] = date('d.m.Y H:i', strtotime(date('d.m.Y H:i') . $resultDate[$key] . ' day'));

                    file_put_contents($log_file, "   resultDate: " . json_encode($resultDate, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
                    $bodyMail = $IP . ' 

 ' . $products . '<br><br>

Всего наименований: ' . $arr[$key] . ', общее количество изделий: ' . $countProd[$key] . ' шт.<br><br>

!!! Крайний срок реализации: ' . $resultDateDeal . '
<br><br><br>

--
<br><br>
С уважением, ' . $respEmpl['result'][0]['NAME'] . ' ' . $respEmpl['result'][0]['LAST_NAME'] .
                        '<br>Тел / whatsapp ' . $respEmpl['result'][0]['WORK_PHONE'];

                    file_put_contents($log_file, "   bodyMail: " . $bodyMail . str_repeat("\r\n", 2), FILE_APPEND);

                    $productsM = 'Поставщик ' . $Comp[$key] . '
Срок реализации: ' . $resultDateDeal . '

' . $IP . '

Товарные позиции:' . $DESCRIPTION_SIMPm[$key] . '
Всего наименований: ' . $arr[$key] . ', общее количество изделий: ' . $countProd[$key] . ' шт.';
                    file_put_contents($log_file, "   productsM: " . $productsM . str_repeat("\r\n", 2), FILE_APPEND);

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
                    file_put_contents($log_file, "   arData: " . json_encode($arData, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

                    $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.activity.add.json';
//                    $result = sendToBx($url, json_encode($arData));

                    $countProd['ext'] = $countProd['ext'] + $countProd[$key];
                    file_put_contents($log_file, "   countProd: " . json_encode($countProd, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
                    $numProd = $numProd + 1;
                    $DESCRIPTION_all = $DESCRIPTION_all . $numProd . '. ' . $productsM . '
';
                    $DESCRIPTION_all = $DESCRIPTION_all . '
------------------------------------------------------------------------------------------------------------------------------------

';
                    file_put_contents($log_file, "   DESCRIPTION_all: " . $DESCRIPTION_all . str_repeat("\r\n", 2), FILE_APPEND);
                };

            }


            //обработка товаров без поставщика
            if (!empty($DESCRIPTION_Sm)) {
                file_put_contents($log_file, "   DESCRIPTION_Sm: " . json_encode($DESCRIPTION_Sm, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
                $countProd['ext'] = $countProd['ext'] + $countProd['NO_PROD'];
                file_put_contents($log_file, "   countProd: " . json_encode($countProd, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
                if (!empty($resultDeal['result']["UF_CRM_1616420219472"])) {
                    //ищем задачу поставщика
                    $arData = ["taskId" => $resultDeal['result']["UF_CRM_1616420219472"]];
                    file_put_contents($log_file, "   ищем задачу поставщика_arData: " . json_encode($arData, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

                    $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/tasks.task.get.json';
                    $resultTask = sendToBx($url, json_encode($arData));
                    file_put_contents($log_file, "   ищем задачу поставщика: " . $resultTask . str_repeat("\r\n", 2), FILE_APPEND);
                    $resultTask = json_decode($resultTask, 1);

                    foreach ($resultGroup["result"] as $value) {
                        file_put_contents($log_file, "   resultGroup: " . json_encode($resultGroup, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
                        if ($value["ID"] == $resultTask["result"]["task"]["groupId"]) {
                            // ищем производителя
                            $arData = ["filter" => ["TITLE" => $value["NAME"]],
                                "select" => ["ID", "TITLE", "EMAIL"],
                                "order" => ["ID" => "asc"]];
                            file_put_contents($log_file, "   ищем производителя_arData: " . json_encode($arData, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

                            $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.company.list.json';
                            $resultComp = sendToBx($url, json_encode($arData));
                            file_put_contents($log_file, "   ищем производителя: " . $resultComp . str_repeat("\r\n", 2), FILE_APPEND);
                            $resultComp = json_decode($resultComp, 1);

                            foreach ($resultComp["result"][0]['EMAIL'] as $mail) {
                                file_put_contents($log_file, "   resultComp: " . json_encode($resultComp, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
                                if ($mail['VALUE_TYPE'] == "WORK") {
                                    $bodyMail = str_replace(array("\r\n", "\r", "\n"), "<br>", $resultTask["result"]["task"]['description']);
                                    $bodyMail = str_replace("Распределить товары по поставщикам, сформировав задачи по группе товаров для каждого из них, <br>	и добавить в \"Задача в проекте (группе)\" этого поставщика", "", $bodyMail);
                                    file_put_contents($log_file, "   bodyMail: " . $bodyMail . str_repeat("\r\n", 2), FILE_APPEND);
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
                                    file_put_contents($log_file, "   отправляем письмо поставщику_arData: " . json_encode($arData, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

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
                    file_put_contents($log_file, "  DESCR: " . $DESCR . str_repeat("\r\n", 2), FILE_APPEND);
                } else {
                    $DESCR = '
	Распределить товары по поставщикам, сформировав задачи по группе товаров для каждого из них, 
	и добавить в "Задача в проекте (группе)" этого поставщика

	' . $IP . '

	Товарные позиции:' . $DESCRIPTION_Sm['NO_PROD'] . '
	Всего наименований: ' . $arr['NO_PROD'] . ', общее количество изделий: ' . $countProd['NO_PROD'] . ' шт.';
                    file_put_contents($log_file, "  DESCR_else: " . $DESCR . str_repeat("\r\n", 2), FILE_APPEND);
                }

            }

            //создаем задачу менеджеру
            $DESCR = $DESCRIPTION_all . $DESCR .
                '

Общее количество товаров: ' . $countProd['ext'];
            file_put_contents($log_file, "  создаем задачу менеджеру: " . $DESCR . str_repeat("\r\n", 2), FILE_APPEND);

            $arData = [
                'fields' => ['TITLE' => $titleTask,
                    'UF_CRM_TASK' => ["D_" . $ID],
                    'GROUP_ID' => '14',
                    "DESCRIPTION" => $DESCR,
                    "DEADLINE" => $resultDateDeal,
                    "RESPONSIBLE_ID" => $resultDeal['result']["ASSIGNED_BY_ID"],
                    "CREATED_BY" => $resultDeal['result']["ASSIGNED_BY_ID"]
                ]
            ];
            file_put_contents($log_file, "   создаем задачу менеджеру_arData: " . json_encode($arData, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);

            $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/tasks.task.add.json';
//            $result= sendToBx($url, json_encode($arData));

        }
        return "ok";
    }

    public function actionTask()
    {
        $log_file = 'task.txt';
        file_put_contents($log_file, date('y-m-d H:i:s') . " !!! Входные данные вебхук task: " . json_encode($_REQUEST, JSON_UNESCAPED_UNICODE) . str_repeat("\r\n", 2), FILE_APPEND);
        return "ok";
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
