<?php

namespace app\models\productManagement;

use yii\helpers\ArrayHelper;
use yii\base\Model;
use Yii;

// Изделие

/**
 * @property int $historyPriorityId
 */
class HistoryProduct extends Model
{
    /**
     * @var int
     */
    public const ENTITY_TYPE_ID = 1042;

    /**
     * @var array<int, int>
     */
    public const PRIORITY_HISTORY_PRODUCT = [
        874 => 754,
        876 => 764,
        878 => 766
    ];
    /**
     * @var int
     */
    public int $id;
    /**
     * @var int
     */
    public int $masterId;
    /**
     * @var int
     */
    public int $statusId;
    /**
     * @var string
     */
    public string $priorityId;
    /**
     * @var int
     */
    public int $productId;
    /**
     * @var int
     */
//    public int $parentId187;
    /**
     * @var string
     */
    public string $operationId;

    /**
     * @param int $id
     * @return HistoryProduct
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
    public static function get(int $id): HistoryProduct
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
     * @param array<string, mixed> $filter
     * @param array<string, mixed> $order
     * @return HistoryProduct[]
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
    public static function list(array $filter = [], array $order = []): array
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
     * @param int $id
     * @param array<string, mixed> $fields
     * @return void
     */
    public static function update(int $id, array $fields)
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
     * @param array<string,mixed> $fields
     * @return void
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
    public static function add(array $fields)
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $history = $obB24->client->call(
            'crm.item.add',
            [
                'entityTypeId' => self::ENTITY_TYPE_ID,
                'fields' => $fields
            ]
        );
    }

    /**
     * @param array<string,mixed> $arr
     * @return HistoryProduct
     * @throws \Exception
     */
    private static function b24ToObject(array $arr): HistoryProduct
    {
        $model = new HistoryProduct();
        $model->id = ArrayHelper::getValue($arr, 'id');
        $model->productId = ArrayHelper::getValue($arr, 'parentId187');
        $model->priorityId = ArrayHelper::getValue($arr, 'ufCrm16Srochnost');
        $model->masterId = ArrayHelper::getValue($arr, 'ufCrm16Master');
        $model->statusId = ArrayHelper::getValue($arr, 'ufCrm16Status');
        $model->operationId = ArrayHelper::getValue($arr, 'ufCrm16Operation');
        return $model;
    }


    /**
     * @param $model
     * @return array
     */
    /*
    private function objectToB24($model)
    {
        $arr = [
            'id' => $model->id,
            'parentId187' => $model->productId,
            'ufCrm16Srochnost' => $model->priorityId,
            'ufCrm16Master' => $model->masterId,
            'ufCrm16Status' => $model->statusId,
            'ufCrm16Operation' => $model->operationId,
        ];
        return $arr;
    }
    */

    /**
     * @return int
     */
    public function getProductPriorityId(): int
    {
        return ArrayHelper::getValue(self::PRIORITY_HISTORY_PRODUCT, $this->priorityId);
    }

    /**
     * @return void
     */
    public static function addEntryToHistory()
    {
    }

    /**
     * @param HistoryProduct $product
     * @return mixed
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
    public static function getLatestProductHistory(HistoryProduct $product)
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $request = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => self::ENTITY_TYPE_ID,
                'order' => ['id' => 'DESC'], // сортировать по убыванию
                'filter' => ['parentId187' => $product->id],
            ]
        )['result']['items'][0];
        return $request;
    }
}
