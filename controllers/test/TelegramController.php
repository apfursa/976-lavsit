<?php


namespace app\controllers\test;

use yii\rest\Controller;

class TelegramController extends Controller
{
    public function actionOne()
    {

        //https://api.telegram.org/bot5933088889:AAHuoHF_wIf69ucFY3asnIi90pPbIJt6NLk/getUpdates
        $token = "5933088889:AAHuoHF_wIf69ucFY3asnIi90pPbIJt6NLk";
        $chat_id = 5222961062;
        $textMessage = "Тестовое сообщение 6";
        $textMessage = urlencode($textMessage);
        $urlQuery = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chat_id . "&text=" . $textMessage;
        $result = file_get_contents($urlQuery);
        return json_decode($result);

        /*
                $token = "5933088889:AAHuoHF_wIf69ucFY3asnIi90pPbIJt6NLk";

                $getQuery = [
        //            "chat_id" 	 => 5222961062,
                    "chat_id" 	 => -805422996,
                    "text"  	 => "Новое сообщение\n из <b>формы</b>",
                    "parse_mode" => "html"
                ];
                $ch = curl_init("https://api.telegram.org/bot". $token ."/sendMessage?" . http_build_query($getQuery));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                $resultQuery = curl_exec($ch);
                curl_close($ch);

                return json_decode($resultQuery);
        */

    }

    public function actionTableBotNext()
    {
        /*токен который выдаётся при регистрации бота */
        $token = "5941725319:AAFYXtweVZcC1MQqDleKGV97o2GrWR4GSRQ";
        $chatId = '805422996';
        $urlPhotoFromB24 =
            "/bitrix/components/bitrix/crm.lead.show/show_file.php?auth=2b71ad630000071b005767400000001800000730d6aff0fdd3a9e9c09af5a5ee209fcc&ownerId=248&fieldName=UF_CRM_1646027703197&dynamic=Y&fileId=8262";
        $arrayQuery = [
//            'chat_id'    => 5222961062,
            "chat_id" 	 => '-' . $chatId, //
            'caption' => 'Проверка работы',
//            'photo' => curl_file_create(__DIR__ . '/irish33.jpg', 'image/jpg' , 'irish33.jpg')
//            'photo' => curl_file_create('../web/photo/irish33.jpg')//, 'image/jpg', 'irish33.jpg'

            'photo' => curl_file_create('https://webmens2.bitrix24.ru' . $urlPhotoFromB24)
        ];
        $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendPhoto');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    public function actionGetUpdate($token)
    {
        // tableBotNext @tableBotNex_bot.
//        $token = "5941725319:AAFYXtweVZcC1MQqDleKGV97o2GrWR4GSRQ";
        $result = file_get_contents('https://api.telegram.org/bot' . $token . '/getUpdates');
        return json_decode($result, true);
    }

    public function actionTwo()
    {

        //https://api.telegram.org/bot5933088889:AAHuoHF_wIf69ucFY3asnIi90pPbIJt6NLk/getUpdates
        $token = "5933088889:AAHuoHF_wIf69ucFY3asnIi90pPbIJt6NLk";
        $chat_id = 5222961062;
        $textMessage = "Тестовое сообщение 6";
        $textMessage = urlencode($textMessage);
        $urlQuery = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chat_id . "&text=" . $textMessage;
        $result = file_get_contents($urlQuery);
        return json_decode($result);

        /*
                $token = "5933088889:AAHuoHF_wIf69ucFY3asnIi90pPbIJt6NLk";

                $getQuery = [
        //            "chat_id" 	 => 5222961062,
                    "chat_id" 	 => -805422996,
                    "text"  	 => "Новое сообщение\n из <b>формы</b>",
                    "parse_mode" => "html"
                ];
                $ch = curl_init("https://api.telegram.org/bot". $token ."/sendMessage?" . http_build_query($getQuery));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                $resultQuery = curl_exec($ch);
                curl_close($ch);

                return json_decode($resultQuery);
        */

    }


}