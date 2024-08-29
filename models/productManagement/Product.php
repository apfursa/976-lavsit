<?php

namespace app\models\productManagement;

use yii\helpers\ArrayHelper;
use yii\base\Model;
use Yii;

// Изделие

/**
 * @property int historyPriorityId
 */
class Product extends Model
{
    /**
     *
     */
    public const ENTITY_TYPE_ID = 187;

    /**
     *
     */
    public const PRIORITY_PRODUCT_HISTORY = [
        754 => 874,
        764 => 876,
        766 => 878
    ];
    /**
     * @var
     */
    public $id;
    /**
     * @var
     */
    public $title;
    /**
     * @var
     */
    public $stageId;
    /**
     * @var
     */
    public $masterId;
    /**
     * @var
     */
    public $statusId;
    /**
     * @var
     */
    public $priorityId;
    /**
     * @var
     */
    public $deadline;

    public $link;

    /**
     * @param $id
     * @return Product
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
     * @return Product[]
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
     * @param $arr
     * @return Product
     * @throws \Exception
     */
    private static function b24ToObject($arr)
    {
        $model = new Product();
        $model->id = ArrayHelper::getValue($arr, 'id');
        $model->title = ArrayHelper::getValue($arr, 'title');
        $model->stageId = ArrayHelper::getValue($arr, 'stageId');
        $model->masterId = ArrayHelper::getValue($arr, 'ufCrm8Master');
        $model->statusId = ArrayHelper::getValue($arr, 'ufCrm8Status');
        $model->priorityId = ArrayHelper::getValue($arr, 'ufCrm8_1705585967731');
        $model->deadline = ArrayHelper::getValue($arr, 'ufCrm8_1701952418411');
        $model->link = ArrayHelper::getValue($arr, 'ufCrm8Link.urlMachine');
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
            'title' => $model->title,
            'stageId' => $model->stageId,
            'ufCrm8Master' => $model->masterId,
            'ufCrm8Status' => $model->statusId,
            'ufCrm8_1705585967731' => $model->priorityId,
            'ufCrm8_1701952418411' => $model->deadline,
        ];
        return $arr;
    }

    /**
     * @return int
     */
    public function getHistoryPriorityId()
    {
        return ArrayHelper::getValue(self::PRIORITY_PRODUCT_HISTORY, $this->priorityId);
    }

    public static function getProductsWithStatusInWork($master)
    {
        $productFilter = [
            'ufCrm8Master' => $master['id'],
            'ufCrm8Status' => 898 // в работе
        ];
        $productOrder = [
            'id' => 'ASC', // сортировать по возрастанию
        ];
        return Product::list($productFilter, $productOrder);
    }

    public static function done($product_id)
    {
        $product = self::get($product_id);
        $workshopId = (new \yii\db\Query())
            ->select(['id'])
            ->from('workshop')
            ->where(['stageId' => $product->stageId])
            ->one()['id'];
        $historyProductUchastok = (new \yii\db\Query())
            ->select(['historyProductUchastok'])
            ->from('workshop')
            ->where(['id' => $workshopId + 1])
            ->one()['historyProductUchastok'];
        $nextStage = (new \yii\db\Query())
            ->select(['stageId'])
            ->from('workshop')
            ->where(['id' => $workshopId + 1])
            ->one()['stageId'];

        $fields = [
            'ufCrm8Master' => '',
            'ufCrm8Status' => 896, // на складе
//                    'stageId' => $nextStage // Перевод изделия на следующую стадию / на следующий участок
        ];
        self::update($product_id, $fields);
        $historyFields = [
            'ufCrm16Master' => $product->masterId,
            'ufCrm16Status' => 880, // на складе
            'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
            'parentId187' => $product->id,
            'ufCrm16Operation' => 920, // перевел на следующий этап
            'ufCrm16Uchastok' => $historyProductUchastok // участок
        ];
        HistoryProduct::add($historyFields);
    }

    public static function returnProduct($product_id)
    {
        $product = self::get($product_id);
        $workshopId = (new \yii\db\Query())
            ->select(['id'])
            ->from('workshop')
            ->where(['stageId' => $product['stageId']])
            ->one()['id'];
        $historyProductUchastok = (new \yii\db\Query())
            ->select(['historyProductUchastok'])
            ->from('workshop')
            ->where(['id' => $workshopId - 1])
            ->one()['historyProductUchastok'];
        $previousStage = (new \yii\db\Query())
            ->select(['stageId'])
            ->from('workshop')
            ->where(['id' => $workshopId - 1])
            ->one()['stageId'];

        $fields = [
            'ufCrm8Master' => '',
            'ufCrm8Status' => 896, // на складе
//                    'stageId' => $previousStage // Перевод изделия на предыдущую стадию / на предыдущий участок
        ];
        self::update($product_id, $fields);
        $historyFields = [
            'ufCrm16Master' => $product->masterId,
            'ufCrm16Status' => 880, // на складе
            'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
            'parentId187' => $product->id,
            'ufCrm16Operation' => 918, // вернул на предыдущий этап
            'ufCrm16Uchastok' => $historyProductUchastok // участок
        ];
        HistoryProduct::add($historyFields);
    }

    public static function startTechnologicalPause($product_id)
    {
        $product = self::get($product_id);
        $historyProductUchastok = (new \yii\db\Query())
            ->select(['historyProductUchastok'])
            ->from('workshop')
            ->where(['stageId' => $product['stageId']])
            ->one()['historyProductUchastok'];
        $fields = [
            'ufCrm8Status' => 902, // технологическая пауза
        ];
        self::update($product_id, $fields);

        $orderHistoryProduct = ['id' => 'DESC']; // В порядке убывания
        $filterHistoryProduct = [
            'ufCrm16Master' => $product->masterId,
        ];
        $historys = HistoryProduct::list($filterHistoryProduct, $orderHistoryProduct);
        if ($historys != []) {
            $history = $historys[0];
            // поставил на технологическую паузу
            if ($history->operationId == 914) {
                return $product;
            }
        }

        $historyFields = [
            'ufCrm16Master' => $product->masterId,
            'ufCrm16Status' => 886, // технологическая пауза
            'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
            'parentId187' => $product->id,
            'ufCrm16Operation' => 914, // поставил на технологическую паузу
            'ufCrm16Uchastok' => $historyProductUchastok // участок
        ];
        HistoryProduct::add($historyFields);
        return $product;
    }

    public static function endTechnologicalPause($product_id)
    {
        $product = self::get($product_id);
        $historyProductUchastok = (new \yii\db\Query())
            ->select(['historyProductUchastok'])
            ->from('workshop')
            ->where(['stageId' => $product['stageId']])
            ->one()['historyProductUchastok'];
        $fields = [
            'ufCrm8Status' => 898, // в работе
        ];
        self::update($product_id, $fields);

        $orderHistoryProduct = ['id' => 'DESC']; // В порядке убывания
        $filterHistoryProduct = [
            'ufCrm16Master' => $product->masterId,
        ];
        $historys = HistoryProduct::list($filterHistoryProduct, $orderHistoryProduct);
        if ($historys != []) {
            $history = $historys[0];
            // поставил на паузу
            if ($history->operationId == 916) {
                return $product;
            }
        }


        $historyFields = [
            'ufCrm16Master' => $product->masterId,
            'ufCrm16Status' => 882, // в работе
            'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
            'parentId187' => $product->id,
            'ufCrm16Operation' => 916, // снял с технологической паузы
            'ufCrm16Uchastok' => $historyProductUchastok // участок
        ];
        HistoryProduct::add($historyFields);
        return $product;
    }

    public static function startPause($product_id)
    {
        $product = self::get($product_id);
        $historyProductUchastok = (new \yii\db\Query())
            ->select(['historyProductUchastok'])
            ->from('workshop')
            ->where(['stageId' => $product['stageId']])
            ->one()['historyProductUchastok'];

        $fields = [
            'ufCrm8Status' => 904, // пауза
        ];
        self::update($product_id, $fields);

        $orderHistoryProduct = ['id' => 'DESC']; // В порядке убывания
        $filterHistoryProduct = [
            'ufCrm16Master' => $product->masterId,
        ];
        $historys = HistoryProduct::list($filterHistoryProduct, $orderHistoryProduct);
        if ($historys != []) {
            $history = $historys[0];
            // поставил на паузу
            if ($history->operationId == 910) {
                return $product;
            }
        }


        $historyFields = [
            'ufCrm16Master' => $product->masterId,
            'ufCrm16Status' => 888, // пауза
            'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
            'parentId187' => $product->id,
            'ufCrm16Operation' => 910, // поставил на паузу
            'ufCrm16Uchastok' => $historyProductUchastok // участок
        ];
        HistoryProduct::add($historyFields);
        return $product;
    }

    public static function endPause($product_id)
    {
        $product = self::get($product_id);
        $historyProductUchastok = (new \yii\db\Query())
            ->select(['historyProductUchastok'])
            ->from('workshop')
            ->where(['stageId' => $product['stageId']])
            ->one()['historyProductUchastok'];
        $fields = [
            'ufCrm8Status' => 898, // в работе
        ];
        self::update($product_id, $fields);

        $orderHistoryProduct = ['id' => 'DESC']; // В порядке убывания
        $filterHistoryProduct = [
            'ufCrm16Master' => $product->masterId,
        ];
        $historys = HistoryProduct::list($filterHistoryProduct, $orderHistoryProduct);
        if ($historys != []) {
            $history = $historys[0];
            // поставил на паузу
            if ($history->operationId == 912) {
                return $product;
            }
        }

        $historyFields = [
            'ufCrm16Master' => $product->masterId,
            'ufCrm16Status' =>  882, // в работе
            'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
            'parentId187' => $product->id,
            'ufCrm16Operation' => 912, // снял с паузы
            'ufCrm16Uchastok' => $historyProductUchastok // участок
        ];
        HistoryProduct::add($historyFields);
        return $product;
    }

}