<?php

namespace app\controllers\test\chatbot;

use app\models\ChatBot;
use app\models\ReportTitleBot;
use Bitrix24\B24Object;
use wm\admin\models\Settings;
use wm\b24tools\b24Tools;
use yii\base\BaseObject;
use yii\console\ExitCode;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use Yii;
use wm\admin\controllers\RestController;

class TestRestController extends RestController
{
    public function actionDate()
    {
        $date = "2022-05-15";
        $res1 = date("Y-m-t", strtotime($date));
//        $res1 = date('d');
        return $res1;
    }

    //задержка на 50 миллисекунд
    public function actionTime_nanosleep()
    {
        time_nanosleep(0, 50000000);//задержка на 50 миллисекунд
        return '';
    }

    public function actionDate1()
    {
//        $a = strtotime('-5 day');
//        $b = date('Y-m-d', $a);
////        $date = "2022-04-01";


        $date = date('d');
        $res1 = (string)((int)date("t", strtotime($date)) - 5);
        if ($date == $res1) {
            return 'Ok';
        } else {
            return 'no';
        }
//        return $res1;
    }

    public function actionHistory($id)
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new B24Object($b24App);
        $request = $obB24->client->call(
            'crm.stagehistory.list',
            [
                'entityTypeId' => 2,
                'filter' => ["OWNER_ID" => $id],
            ]
        )['result']['items'];
        return $request;
    }

    public function actionNext_day()
    {
        $nextDay1 = strtotime('+1 day');
        $nextDay = date('d.m.Y 18:00', $nextDay1);
    }

    public function actionSubstr($p)
    {
        $date = substr($p, 3, -19);
//        $temp = explode(',', $data);
//        foreach ($temp as $value) {
//            preg_match('/^(<>|>=|>|<=|<|=)/', $value, $matches);
//            $operator = $matches[1];
//            $date[] = substr($value, strlen($operator));
//        }
        return $date;
    }

    public function actionQqd2()
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $request = $obB24->client->call(
            'lists.element.get',
            [
                'IBLOCK_TYPE_ID' => 'bitrix_processes',
                'IBLOCK_ID' => '31',
            ],
        );//['result']
        $countCalls = (int)ceil($request['total'] / $obB24->client::MAX_BATCH_CALLS);
        $res = $request['result'];
        for ($i = 1; $i < $countCalls; $i++) {
            $obB24->client->addBatchCall(
                'lists.element.get',
                [
                    'IBLOCK_TYPE_ID' => 'bitrix_processes',
                    'IBLOCK_ID' => '31',
                    'start' => $obB24->client::MAX_BATCH_CALLS * $i
                ],
                function ($result) use (&$res) {
                    $res = array_merge($res, $result['result']);
                });
        }
        $obB24->client->processBatchCalls();
        foreach ($res as $oneEntity) {
            $model = QualityControlData::find()->where(['id' => $oneEntity['ID']])->one();
            if (!$model) {
                $model = new QualityControlData();
            }
            $convertedFieldsOneEntity = [];
            foreach ($oneEntity as $key => $value) {
                $a = \yii\helpers\Inflector::variablize(strtolower($key));
                $convertedFieldsOneEntity[$a] = $value;
            }
            foreach ($convertedFieldsOneEntity as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $v) {
                        $model->$key = $v;
                    }
                } else {
                    $model->$key = $val;
                }
            }
            $model->dateCreate = date("Y-m-d H:i:s", strtotime($model->dateCreate));
            $model->timestampX = date("Y-m-d H:i:s", strtotime($model->timestampX));
            $model->save();
        }
        return 'Ok';
    }
    public function actionTestarr()
    {
       $a = [2,23,4,56,34,5,87,86,88,89,12,13,14,15,16,17,18,19,20,21,22,24,25,26,27,28,29,30,31,32,33];
       sort($a);
       $b = array_chunk($a,10);

       return $b;
    }

    public function actionIsset1()
    {
//        $a = null;
        $b = isset($a);
        return $b;
    }

    public function actionEmpty1()
    {
        $a = [1, 5];
        $b = empty($a);
        return $b;
    }

    public function actionGetCollumn()
    {
        $a = new Testbd();
        $b = $a->attributes();
        return $b;
    }

    public function actionStrToArr()
    {
        $string = '1,7,9,12,256';
        $arrStr = explode(',', $string);
        foreach ($arrStr as $oneStr) {
            $arrInt[] = (int)$oneStr;
        }
        return $arrInt;
    }

    public function actionArrToStr()
    {
        $arrInt = [1, 7, 9, 12, 256];
        foreach ($arrInt as $oneInt) {
            $arrStr[] = (string)$oneInt;
        }
        $gtring = implode(',', $arrStr);
        return $gtring;
    }

    public function actionDeyWeek()
    {
        $n = date("w", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
        return $n; // День недели возвращается в виде строки ("0"=Воскресенье, "1"=Понедельник и т.д.)
    }

    public function actionSwitch($id)
    {
        switch ($id) {
            case('1'):
                $result = '1+1';
                break;
            case('2'):
                $result = '2+2';
                break;
            default:
                $result = '3+3';
        }
        return $result;
    }

    public static function actionGetDateWeekly()
    {
        $date = [];
        $finish = date('Y-m-d');
        $nextDate = time() - (7 * 24 * 60 * 60);
        $start = date('Y-m-d', $nextDate);
        $date[0] = '>=' . '"' . $start . '"';
        $date[1] = '<' . '"' . $finish . '"';
        return $date;
    }

    public function actionDate2()
    {
        $a = strtotime('-1 month');
        $b = date('Y-m-d', $a);
        return $b;
////        $date = "2022-04-01";


//        $date = date('d');
//        $res1 = (string)((int)date("t", strtotime($date)) - 5);
//        if ($date == $res1) {
//            return 'Ok';
//        } else {
//            return;
//        }
//        return $res1;
    }

    public function actionVar()
    {
        $a = ['bhfghf', 'jfrdsgfuhregh', 'jhfgjr'];
//        $test = [];
        foreach ($a as $b) {
            $test[] = $b;
        }
        return $test;
    }

    public function actionFor()
    {
        $b = 0;
        $c = 0;
        $m = [1, 5, 'ikfhg', 'dhfuyh', [4, true, 'fgr', null], null];

        for ($i = 3; $i < 12; $i++) {
            $b = $b + $i * 2;
        }

        for ($i = 3; $i < 12; $i++) $c = $c + $i * 2;

//        foreach ($m as $v) {
//            $temp[] = $v == null ? 300 : 50;
//        }

//        foreach ($m as $v) $temp[] = $v == null ? 300 : 50;

        foreach ($m as $v) if ($v != null) $temp[] = $v;

        return $temp;
    }

    public function actionUserCurrent()
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'user.current',
        )['result'];
        return $answerB24;
    }

    public function actionTimeman_schedule_get()
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'timeman.schedule.get',
            [
                "id" => 4
            ],
        );
        return $answerB24;
    }

    public function actionTimeman_status()
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'timeman.status',
            [
                "USER_ID" => 6
            ],
        );
        return $answerB24;
    }

    public function actionTimeman_settings()
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'timeman.settings',
            [
                "USER_ID" => 6//
            ],
        );
        return $answerB24;
    }

    public function actionUser_get($id = 6)
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'user.get',
            [
                "ID" => $id//
            ],
        );
        return $answerB24;
    }

    public function actionIm_dialog_users_list($dialog_id = 'chat94')
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'im.dialog.users.list',
            [
                "DIALOG_ID" => $dialog_id
            ],
        );
        return $answerB24;
    }

    public function actionIm_chat_user_list($chat_id = '94')
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'im.chat.user.list',
            [
                "CHAT_ID" => $chat_id
            ],
        );
        return $answerB24;
    }

    public function actionIm_chat_get()
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'im.chat.get',
            [
                'ENTITY_TYPE' => 'LINES',
                'ENTITY_ID' => 'livechat|2|92|48'
            ],
        );
        return $answerB24;
    }

    public function actionIm_chat_user_add()
    {
        $auth = [
            'access_token' => '4a9da36600645fc20062e8060000002400000746ad8e814cc45f96822fc286f3d6ed3e',
            'expires' => '1721998666',
            'expires_in' => '3600',
            'scope' => 'crm,rpa,imbot,cashbox,catalog,telephony,call,timeman,task,tasks_extended,log,sonet_group,delivery,pay_system,placement,user,user_brief,user_basic,user.userfield,entity,pull,pull_channel,mobile,messageservice,mailservice,lists,landing,landing_cloud,department,contact_center,imopenlines,im,forum,faceid,documentgenerator,disk,calendar,bizproc,userconsent,rating,smile,userfieldconfig,biconnector,calendarmobile,iblock,im.import,imconnector,intranet,notifications,configuration.import,sale,salescenter,socialnetwork,tasks,tasksmobile',
            'domain' => 'webmens3.bitrix24.ru',
            'server_endpoint' => 'https://oauth.bitrix.info/rest/',
            'status' => 'L',
            'client_endpoint' => 'https://webmens3.bitrix24.ru/rest/',
            'member_id' => 'f3b1042cf2591ec2fb69d3b75a6b8964',
            'refresh_token' => '3a1ccb6600645fc20062e80600000024000007cacf03d326341abdc2148b16554e65a2',
            'user_id' => '36',
            'client_id' => 'local.647daa7db267c2.13192462',
            'application_token' => 'f6997e09354e1a81ace49b7db2d8caef',
        ];
        $component = new b24Tools();
        $b24App = $component->connectFromUser($auth);
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'im.chat.user.add',
            [
                'CHAT_ID' => '94',
                'USERS' => [6]
            ],
        );
        return $answerB24;
    }

    public function actionCalendar_resource_list()
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'calendar.resource.list',
        );
        return $answerB24;
    }


}