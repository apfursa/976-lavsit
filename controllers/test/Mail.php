<?
$log_file = $_SERVER['DOCUMENT_ROOT'] . '/TaskStep2.log.txt';
file_put_contents($log_file, date('y-m-d H:i:s') . " !!! Входные данные send_step2: " . json_encode($_REQUEST) . str_repeat("\r\n", 2), FILE_APPEND);

function error_msg($err_type, $err_msg, $err_file, $err_line)
{
    static $countErr = 0;
    $countErr++;

    $msgErr = "Ошибка №$countErr: Извините, но на этой странице возникла ошибка. Тип ошибки: $err_type, сообщение: $err_msg, файл: $err_file, номер строки: $err_line";

    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/TaskStep2.log.txt', date('y-m-d H:i:s') . print_r($msgErr, true) . str_repeat("\r\n", 2), FILE_APPEND);
}

// Регистрируем нашу функцию в качестве обработчика ошибок
set_error_handler("error_msg");

function sendToBx($url, $data = null)
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

$ID = $_REQUEST['ID'];
$RESPONSIBLE_ID = $_REQUEST['RESPONSIBLE_ID'];
$arData = ["ID" => $ID];

//ищем сделку
$url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.deal.get.json';
$resultDeal = sendToBx($url, json_encode($arData));
file_put_contents($log_file, "    ищем сделку: " . $resultDeal . str_repeat("\r\n", 2), FILE_APPEND);
$resultDeal = json_decode($resultDeal, 1);
$resultDateDeal = date('d.m.Y', strtotime($resultDeal['result']["UF_CRM_1571825141958"] . ' -4 days'));

//ищем ИП
$IP = "";
if ($resultDeal['result']['UF_CRM_1624975584152']) {
    $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.deal.fields.json';
    $IPs = sendToBx($url);
    $IPs = json_decode($IPs, 1);
    foreach ($IPs['result']['UF_CRM_1624975584152']['items'] as $arrIP) {
        if ($arrIP['ID'] == $resultDeal['result']['UF_CRM_1624975584152']) {
            $IP = 'ИП: ' . $arrIP['VALUE'];
        }
    }
}

// получим данные сотрудника (ответственного)
$respEmplData = array("FILTER" => array("ID" => $resultDeal['result']["ASSIGNED_BY_ID"]));

$url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/user.get.json';
$respEmpl = sendToBx($url, json_encode($respEmplData));
file_put_contents($log_file, "     получим данные сотрудника (ответственного): " . $respEmpl . str_repeat("\r\n", 2), FILE_APPEND);
$respEmpl = json_decode($respEmpl, 1);

$DESCRIPTION_all = '--------------------------------------------------Товарные позиции--------------------------------------------------

';

$numProd = 0;
$DESCRIPTION = [];
$DESCRIPTION_SIMP = [];
$DESCRIPTION_Sm = [];
$DESCR = "";
$countProd = "";
/*if ($resultDeal['result']['UF_CRM_1624975584152'])
	{*//*$IPs = file_get_contents('https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.deal.fields.json', false, stream_context_create(array(
		'http' => array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/json'
		)
	)));*/
/*$url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.deal.fields.json';
$IPs = sendToBx($url);
$IPs = json_decode($IPs,1);
foreach ($IPs['result']['UF_CRM_1624975584152'] as $arrIP )
{
    if ($arrIP['ID'] == $resultDeal['result']['UF_CRM_1624975584152']) {$IP = 'ИП: '.$arrIP['VALUE'];}
}}*/

// ищем товары
$url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.deal.productrows.get.json';
$resultOrder = sendToBx($url, json_encode($arData));
file_put_contents($log_file, "    ищем товары: " . $resultOrder . str_repeat("\r\n", 2), FILE_APPEND);
$resultOrder = json_decode($resultOrder, 1);

//если в сделке есть товары
if (!empty($resultOrder["result"])) {
    //получим массив групп на портале
    $arData = ['ORDER' => ['NAME' => 'ASC'],
        'FILTER' => ["ACTIVE" => "Y"],
        'IS_ADMIN' => 'Y'];

    $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/sonet_group.get.json';
    $resultGroup = sendToBx($url);
    $resultGroup = json_decode($resultGroup, 1);

    //читаем резалтный массив
    foreach ($resultOrder["result"] as $arrProd) {
        $arData = ["ID" => $arrProd['PRODUCT_ID']];
        // ищем товар
        $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.product.get.json';
        $resultProd = sendToBx($url, json_encode($arData));
        file_put_contents($log_file, "    ищем товар: " . $resultProd . str_repeat("\r\n", 2), FILE_APPEND);
        $resultProd = json_decode($resultProd, 1);

        // переименуем название товара для поставщика
        if ($resultProd["result"]['PROPERTY_166']["value"] == null) {
            $nameProd = $arrProd['PRODUCT_NAME'];
        } else {
            $nameProd = quotemeta($arrProd['ORIGINAL_PRODUCT_NAME']);
            file_put_contents($log_file, "    переименуем название товара для поставщика: " . $nameProd . str_repeat("\r\n", 2), FILE_APPEND);
            $nameProd = preg_replace("/" . $nameProd . "/", "", $arrProd['PRODUCT_NAME']);
            $nameProd = $resultProd["result"]['PROPERTY_166']["value"] . $nameProd;
        };

        if (!empty($resultProd["result"]['PROPERTY_156']["value"])) {
            $idP = $resultProd["result"]['PROPERTY_156']["value"];

            // ищем производителя
            $arData = ["ID" => $idP];

            $url = 'https://lavsit.bitrix24.ru/rest/1/u6nvxarzrppus36a/crm.company.get.json';
            $resultComp = sendToBx($url, json_encode($arData));
            file_put_contents($log_file, "    ищем производителя: " . $resultComp . str_repeat("\r\n", 2), FILE_APPEND);
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
                    };
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
                    };

                }
            };
        };
    };

    $titleTask = count($DESCRIPTION) + count($DESCRIPTION_SIMP) + count($DESCRIPTION_Sm);
    if ($titleTask > 1) {
        $titleTask = $ID . ' + (' . $titleTask . ')';
    } else {
        $titleTask = $ID;
    };


    //обработка товаров экстранетов
    if (!empty($DESCRIPTION)) {
        //создаем письмо из задачи
        foreach ($DESCRIPTION as $key => $product) {
            $resultDate[$key] = date('d.m.Y H:i', strtotime(date('d.m.Y H:i') . $resultDate[$key] . ' day'));

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

            //file_put_contents($log_file, "    итоговая строка письма: ".$bodyMail.str_repeat("\r\n", 2), FILE_APPEND);

            //ищем задачу поставщика
            $arData = ["filter" => ["TITLE" => $ID . ' / ' . $Comp[$key]],
                "select" => ["ID", "TITLE", "DESCRIPTION"],
                "order" => ["ID" => "asc"]];

            $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/tasks.task.list.json';
            $result = sendToBx($url, json_encode($arData));
            file_put_contents($log_file, "   ищем задачу поставщика: " . $result . str_repeat("\r\n", 2), FILE_APPEND);
            $result = json_decode($result, 1);

            $countProd['ext'] = $countProd['ext'] + $countProd[$key];
            $numProd = $numProd + 1;

            if (!empty($result["result"]["tasks"][0])) {
                file_put_contents($log_file, "    задача экстранета найдена " . str_repeat("\r\n", 2), FILE_APPEND);

                //отправка письма поставщику
                $bodyMail = str_replace(array("\r\n", "\r", "\n"), "<br>", $result["result"]["tasks"][0]['description']);
                //file_put_contents($log_file, "   текст письма поставщика: ".$bodyMail.str_repeat("\r\n", 2), FILE_APPEND);
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
                $resultMess = sendToBx($url, json_encode($arData));

                $DESCRIPTION_all = $DESCRIPTION_all . $numProd . '. ' . $productM . '
						ссылка на задачу: https://lavsit.bitrix24.ru/workgroups/group/' . $key . '/tasks/task/view/' . $result["result"]["tasks"][0]['id'] . '/ 
						';
                $DESCRIPTION_all = $DESCRIPTION_all . '
					
					------------------------------------------------------------------------------------------------------------------------------------
						
						';

            } else {
                file_put_contents($log_file, "    задача экстранета не найдена " . str_repeat("\r\n", 2), FILE_APPEND);
                //ищем ответственнного среди экстранета по почте!!!
                $arData = ['FILTER' => ['USER_TYPE' => "extranet"]];

                $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/user.search.json';
                $result = sendToBx($url, json_encode($arData));
                file_put_contents($log_file, "    экстранет: " . $result . str_repeat("\r\n", 2), FILE_APPEND);
                $result = json_decode($result, 1);

                $RESPONSIBLE = $RESPONSIBLE_ID;
                foreach ($result['result'] as $respUs) {
                    if ($respUs['EMAIL'] == $CompMail[$key]) {
                        $RESPONSIBLE = $respUs["ID"];
                    };
                };

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

                $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/tasks.task.add.json';
                $result = sendToBx($url, json_encode($arData));
                file_put_contents($log_file, "    создана задача поставщику: " . $Comp[$key] . ':    ' . $result . str_repeat("\r\n", 2), FILE_APPEND);
                $result = json_decode($result, 1);

                $DESCRIPTION_all = $DESCRIPTION_all . $numProd . '. ' . $productM . '
	ссылка на задачу: https://lavsit.bitrix24.ru/workgroups/group/' . $key . '/tasks/task/view/' . $result['result']['task']['id'] . '/ 
	';
                $DESCRIPTION_all = $DESCRIPTION_all . '

------------------------------------------------------------------------------------------------------------------------------------
	
	';

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
                $result = sendToBx($url, json_encode($arData));
                file_put_contents($log_file, "    отправлено письмо поставщику: " . $Comp[$key] . ':    ' . $result . str_repeat("\r\n", 2), FILE_APPEND);

            }

            if ($dateResD < $resultDate[$key]) {
                $dateResD = $resultDate[$key];
            }

        };

    };


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
            $result = sendToBx($url, json_encode($arData));
            file_put_contents($log_file, "    отправлено письмо поставщику: " . $Comp[$key] . ':    ' . $result . str_repeat("\r\n", 2), FILE_APPEND);

            $countProd['ext'] = $countProd['ext'] + $countProd[$key];
            $numProd = $numProd + 1;
            $DESCRIPTION_all = $DESCRIPTION_all . $numProd . '. ' . $productsM . '
';
            $DESCRIPTION_all = $DESCRIPTION_all . '
------------------------------------------------------------------------------------------------------------------------------------

';
        };

    };
    //обработка товаров без поставщика
    if (!empty($DESCRIPTION_Sm)) {
        $countProd['ext'] = $countProd['ext'] + $countProd['NO_PROD'];
        if (!empty($resultDeal['result']["UF_CRM_1616420219472"])) {
            //ищем задачу поставщика
            $arData = ["taskId" => $resultDeal['result']["UF_CRM_1616420219472"]];

            $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/tasks.task.get.json';
            $resultTask = sendToBx($url, json_encode($arData));
            file_put_contents($log_file, "   ищем задачу поставщика: " . $result . str_repeat("\r\n", 2), FILE_APPEND);
            $resultTask = json_decode($resultTask, 1);

            foreach ($resultGroup["result"] as $value) {
                if ($value["ID"] == $resultTask["result"]["task"]["groupId"]) {    // ищем производителя
                    $arData = ["filter" => ["TITLE" => $value["NAME"]],
                        "select" => ["ID", "TITLE", "EMAIL"],
                        "order" => ["ID" => "asc"]];

                    $url = 'https://lavsit.bitrix24.ru/rest/1/t0bc7agx6ofnsxgg/crm.company.list.json';
                    $resultComp = sendToBx($url, json_encode($arData));
                    file_put_contents($log_file, "    ищем производителя: " . $resultComp . str_repeat("\r\n", 2), FILE_APPEND);
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
                            $result = sendToBx($url, json_encode($arData));
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
    $result = sendToBx($url, json_encode($arData));
};
?>
