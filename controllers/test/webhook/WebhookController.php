<?php

namespace app\controllers\test\webhook;

use app\models\ReportCompanySearch;
use wm\yii\rest\ActiveRestController;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\helpers\Url;
use Yii;


class WebhookController extends Controller
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
            "userId" 	 => $userId,
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


}
