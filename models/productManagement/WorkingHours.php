<?php

namespace app\models\productManagement;

use yii\helpers\ArrayHelper;
use yii\base\Model;
use Yii;

// Рабочее время

/**
 *
 */
class WorkingHours extends Model
{
    /**
     *
     */
    public const ENTITY_TYPE_ID = 1046;
    public const COMPLETED_BY_USER = 890;
    public const COMPLETED_AUTOMATICALLY = 892;
    public const FIXED_BY_USER = 894;
    /**
     * @var
     */
    public $id;
    /**
     * @var
     */
    public $masterId;
    /**
     * @var
     */
    public $dateStart;
    /**
     * @var
     */
    public $timeStart;
    /**
     * @var
     */
    public $dateEnd;
    /**
     * @var
     */
    public $timeEnd;
    /**
     * @var
     */
    public $typeOfCompletion;

    /**
     * @param $id
     * @return WorkingHours
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
    public static function get($id)
    {
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

    /**
     * @param $filter
     * @param $order
     * @return WorkingHours[]
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
    public static function list($filter = [], $order = [])
    {
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
        $arr = [];
        foreach ($request as $item) {
            $arr[] = self::b24ToObject($item);
        }
        return $arr;
    }

    /**
     * @param $id
     * @param $fields
     * @return void
     */
    public static function update($id, $fields)
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'crm.item.update',
            [
                'entityTypeId' => self::ENTITY_TYPE_ID,
                'id' => $id,
                'fields' => $fields
            ]
        );
    }

    /**
     * @param $id
     * @param $fields
     * @return void
     */
    public static function add($master)
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $history = $obB24->client->call(
            'crm.item.add',
            [
                'entityTypeId' => self::ENTITY_TYPE_ID,
                'fields' => [
                    'ufCrm18Master' => $master['id'],
                    'ufCrm18PrishelDate' => date('Y-m-d'),
                    'ufCrm18PrishelTime' => date('H:i:s'),
                ]
            ]
        );
    }

    /**
     * @param $arr
     * @return WorkingHours
     * @throws \Exception
     */
    private static function b24ToObject($arr)
    {
        $model = new WorkingHours();
        $model->id = ArrayHelper::getValue($arr, 'id');
        $model->masterId = ArrayHelper::getValue($arr, 'ufCrm18Master');
        $model->dateStart = ArrayHelper::getValue($arr, 'ufCrm18PrishelDate');
        $model->timeStart = ArrayHelper::getValue($arr, 'ufCrm18PrishelTime');
        $model->dateEnd = ArrayHelper::getValue($arr, 'ufCrm18UshelData');
        $model->timeEnd = ArrayHelper::getValue($arr, 'ufCrm18UshelTime');
        $model->typeOfCompletion = ArrayHelper::getValue($arr, 'ufCrm18TerminatioType');
        return $model;
    }

    /**
     * @param $model
     * @return array
     */
    private function objectToB24($model)
    {
        $arr = [
            'id' => $model->id,
            'ufCrm18Master' => $model->masterId,
            'ufCrm18PrishelDate' => $model->dateStart,
            'ufCrm18PrishelTime' => $model->timeStart,
            'ufCrm18UshelData' => $model->dateEnd,
            'ufCrm18UshelTime' => $model->timeEnd,
            'ufCrm18TerminatioType' => $model->typeOfCompletion,
        ];
        return $arr;
    }

    public static function howWorkingDayCompleted($master)
    {
        $filter = [
            'ufCrm18Master' => $master['id'],
        ];
        $order = [
            'id' => 'DESC', // сортировать по убыванию
        ];
        $models = self::list($filter, $order);
        if ($models == []) {
            return 'no entry';
        } else {
            $model = $models[0];
        }
        if ($model->timeEnd == '') {
            return 'not completed';
        } else {
            if ($model->typeOfCompletion == self::COMPLETED_BY_USER) {
                return 'completed by the user';
            }
            if ($model->typeOfCompletion == self::COMPLETED_AUTOMATICALLY) {
                return 'completed automatically';
            }
            if ($model->typeOfCompletion == self::FIXED_BY_USER) {
                return 'fixed by user';
            }
        }
    }

    public static function endWorkingDay($product_id, $master_id)
    {
        if ($product_id) {
            $product = Product::get($product_id);
            $filter = ['ufCrm18Master' => $product->masterId];
            $order = ['id' => 'DESC']; // В порядке убывания
            $models = self::list($filter, $order);
            if ($models) {
                $model = $models[0];
                $fields = [
                    'ufCrm18TerminatioType' => self::COMPLETED_BY_USER,
                    'ufCrm18UshelData' => date('Y-m-d'),
                    'ufCrm18UshelTime' => date('H:i:s')
                ];
                self::update($model->id, $fields);
            }
            $historyProductUchastok = (new \yii\db\Query())
                ->select(['historyProductUchastok'])
                ->from('workshop')
                ->where(['stageId' => $product->stageId])
                ->one()['historyProductUchastok'];
            $historyProductStatus = (new \yii\db\Query())
                ->select(['ufCrm16Status'])
                ->from('statusProduct')
                ->where(['ufCrm8Status' => $product->statusId])
                ->one()['ufCrm16Status'];
            $historyFields = [
                'ufCrm16Master' => $product->masterId,
                'ufCrm16Status' => $historyProductStatus,
                'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
                'parentId187' => $product->id,
                'ufCrm16Operation' => 908, // конец рабочего дня
                'ufCrm16Uchastok' => $historyProductUchastok // участок
            ];
            HistoryProduct::add($historyFields);
        }
        if ($master_id) {
            $filter = ['ufCrm18Master' => $master_id];
            $order = ['id' => 'DESC']; // В порядке убывания
            $models = self::list($filter, $order);
            if ($models) {
                $model = $models[0];
                $fields = [
                    'ufCrm18TerminatioType' => self::COMPLETED_BY_USER,
                    'ufCrm18UshelData' => date('Y-m-d'),
                    'ufCrm18UshelTime' => date('H:i:s')
                ];
                self::update($model->id, $fields);
            }
        }
    }
}