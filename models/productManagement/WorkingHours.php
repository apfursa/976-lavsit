<?php

namespace app\models\productManagement;

use yii\helpers\ArrayHelper;
use yii\base\Model;
use Yii;

// Изделие
class WorkingHours extends Model
{
    public const ENTITY_TYPE_ID = 1046;
    public $id;
    public $title;
    public $stageId;
    public $masterId;
    public $statusId;
    public $priorityId;
    public $deadline;

    public static function get($id){
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'crm.item.get',
            [
                'entityTypeId' => self::ENTITY_TYPE_ID,
                'id' => $id
            ]
        )['result']['item'];
        return self::b24ToObject($answerB24);
    }
    public static function list($filter = [], $order = []){
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $request = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => self::ENTITY_TYPE_ID,
                'order' => $order,
                'filter' => $filter
            ]
        )['result']['items'];
        $arr=[];
        foreach ($request as $item){
            $arr[] = self::b24ToObject($item);
        }
        return $arr;
    }
    public function update($id, $fields){

    }

    private static function b24ToObject($arr){
        $model = new WorkingHours();
        $model->id = ArrayHelper::getValue($arr, 'id');
        $model->title = ArrayHelper::getValue($arr, 'title');
        $model->stageId = ArrayHelper::getValue($arr, 'stageId');
        $model->masterId = ArrayHelper::getValue($arr, 'ufCrm8Master');
        $model->statusId = ArrayHelper::getValue($arr, 'ufCrm8Status');
        $model->priorityId = ArrayHelper::getValue($arr, 'ufCrm8_1705585967731');
        $model->deadline = ArrayHelper::getValue($arr, 'ufCrm8_1701952418411');
        return $model;
    }
    private function objectToB24($model){
        $arr = [
            'id' => $model->id,
            'title' => $model->title,
            'stageId' => $model->stageId,
            'ufCrm8Master' => $model->masterId,
            'ufCrm8Status' => $model->statusId,
            'ufCrm8_1705585967731' => $model->priorityId,
            'ufCrm8_1701952418411' => $model->deadline,
        ];
        return $arr;
    }

}