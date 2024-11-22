<?php

namespace app\models\productManagement;

use Bitrix24\Exceptions\Bitrix24ApiException;
use Bitrix24\Exceptions\Bitrix24EmptyResponseException;
use Bitrix24\Exceptions\Bitrix24Exception;
use Bitrix24\Exceptions\Bitrix24IoException;
use Bitrix24\Exceptions\Bitrix24MethodNotFoundException;
use Bitrix24\Exceptions\Bitrix24PaymentRequiredException;
use Bitrix24\Exceptions\Bitrix24PortalDeletedException;
use Bitrix24\Exceptions\Bitrix24PortalRenamedException;
use Bitrix24\Exceptions\Bitrix24SecurityException;
use Bitrix24\Exceptions\Bitrix24TokenIsExpiredException;
use Bitrix24\Exceptions\Bitrix24TokenIsInvalidException;
use Bitrix24\Exceptions\Bitrix24WrongClientException;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use Yii;

// Изделие

/**
 * @property int $historyPriorityId
 */
class Product extends Model

{
    /**
     * @var int
     */
    public const ENTITY_TYPE_ID = 187; // СП "Производство София"

    /**
     * @var array<int, int>
     */
    public const PRIORITY_PRODUCT_HISTORY = [
        754 => 874,
        764 => 876,
        766 => 878
    ];
    /**
     * @var int
     */
    public ?int $id;
    /**
     * @var string
     */
    public ?string $title;
    /**
     * @var string
     */
    public ?string $stageId;
    /**
     * @var string
     */
    public ?string $masterId;
    /**
     * @var string
     */
    public ?string $statusId;
    /**
     * @var string
     */
    public ?string $priorityId;
    /**
     * @var string
     */
    public ?string $deadline;

    /**
     * @var string
     */
    public ?string $link;

    /**
     * @param int $id
     * @return Product
     * @throws Bitrix24ApiException
     * @throws Bitrix24EmptyResponseException
     * @throws Bitrix24Exception
     * @throws Bitrix24IoException
     * @throws Bitrix24MethodNotFoundException
     * @throws Bitrix24PaymentRequiredException
     * @throws Bitrix24PortalDeletedException
     * @throws Bitrix24PortalRenamedException
     * @throws Bitrix24SecurityException
     * @throws Bitrix24TokenIsExpiredException
     * @throws Bitrix24TokenIsInvalidException
     * @throws Bitrix24WrongClientException
     * @throws Exception
     * @throws \yii\db\Exception
     * @throws \Exception
     */
    public static function get(int $id): Product
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
     * @return Product[]
     * @throws Bitrix24ApiException
     * @throws Bitrix24EmptyResponseException
     * @throws Bitrix24Exception
     * @throws Bitrix24IoException
     * @throws Bitrix24MethodNotFoundException
     * @throws Bitrix24PaymentRequiredException
     * @throws Bitrix24PortalDeletedException
     * @throws Bitrix24PortalRenamedException
     * @throws Bitrix24SecurityException
     * @throws Bitrix24TokenIsExpiredException
     * @throws Bitrix24TokenIsInvalidException
     * @throws Bitrix24WrongClientException
     * @throws Exception
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
        Yii::warning(ArrayHelper::toArray($request), 'Product_144');
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
     * @param array<string, mixed> $arr
     * @return Product
     * @throws \Exception
     */
    private static function b24ToObject(array $arr): Product
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
    /*
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
    */

    /**
     * @return int
     */
    public function getHistoryPriorityId(): ?int
    {
        return ArrayHelper::getValue(self::PRIORITY_PRODUCT_HISTORY, $this->priorityId);
    }

    /**
     * @param array<string, mixed> $master
     * @return Product[]
     * @throws Bitrix24ApiException
     * @throws Bitrix24EmptyResponseException
     * @throws Bitrix24Exception
     * @throws Bitrix24IoException
     * @throws Bitrix24MethodNotFoundException
     * @throws Bitrix24PaymentRequiredException
     * @throws Bitrix24PortalDeletedException
     * @throws Bitrix24PortalRenamedException
     * @throws Bitrix24SecurityException
     * @throws Bitrix24TokenIsExpiredException
     * @throws Bitrix24TokenIsInvalidException
     * @throws Bitrix24WrongClientException
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public static function getProductsWithStatusInWork(array $master): array
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

    /**
     * @param int $product_id
     * @return void
     * @throws Bitrix24ApiException
     * @throws Bitrix24EmptyResponseException
     * @throws Bitrix24Exception
     * @throws Bitrix24IoException
     * @throws Bitrix24MethodNotFoundException
     * @throws Bitrix24PaymentRequiredException
     * @throws Bitrix24PortalDeletedException
     * @throws Bitrix24PortalRenamedException
     * @throws Bitrix24SecurityException
     * @throws Bitrix24TokenIsExpiredException
     * @throws Bitrix24TokenIsInvalidException
     * @throws Bitrix24WrongClientException
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public static function done(int $product_id)
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
                    'stageId' => $nextStage // Перевод изделия на следующую стадию / на следующий участок
        ];
        self::update($product_id, $fields);
        $historyFields = [
            'ufCrm16Master' => $product->masterId,
            'ufCrm16Status' => 880, // на складе
            'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
            'parentId187' => $product->id,
            'ufCrm16Operation' => 920, // перевел на следующий этап
            'ufCrm16Uchastok' => $historyProductUchastok, // участок
            'ufCrm16Dateandtime' => date('d-m-Y H:i:s'), // дата и время
        ];
        HistoryProduct::add($historyFields);
    }

    /**
     * @param int $product_id
     * @return void
     * @throws Bitrix24ApiException
     * @throws Bitrix24EmptyResponseException
     * @throws Bitrix24Exception
     * @throws Bitrix24IoException
     * @throws Bitrix24MethodNotFoundException
     * @throws Bitrix24PaymentRequiredException
     * @throws Bitrix24PortalDeletedException
     * @throws Bitrix24PortalRenamedException
     * @throws Bitrix24SecurityException
     * @throws Bitrix24TokenIsExpiredException
     * @throws Bitrix24TokenIsInvalidException
     * @throws Bitrix24WrongClientException
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public static function returnProduct(int $product_id)
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
                    'stageId' => $previousStage // Перевод изделия на предыдущую стадию / на предыдущий участок
        ];
        self::update($product_id, $fields);
        $historyFields = [
            'ufCrm16Master' => $product->masterId,
            'ufCrm16Status' => 880, // на складе
            'ufCrm16Srochnost' => $product->getHistoryPriorityId(), // срочность
            'parentId187' => $product->id,
            'ufCrm16Operation' => 918, // вернул на предыдущий этап
            'ufCrm16Uchastok' => $historyProductUchastok, // участок
            'ufCrm16Dateandtime' => date('d-m-Y H:i:s'), // дата и время
        ];
        HistoryProduct::add($historyFields);
    }

    /**
     * @param int $product_id
     * @return Product
     * @throws Bitrix24ApiException
     * @throws Bitrix24EmptyResponseException
     * @throws Bitrix24Exception
     * @throws Bitrix24IoException
     * @throws Bitrix24MethodNotFoundException
     * @throws Bitrix24PaymentRequiredException
     * @throws Bitrix24PortalDeletedException
     * @throws Bitrix24PortalRenamedException
     * @throws Bitrix24SecurityException
     * @throws Bitrix24TokenIsExpiredException
     * @throws Bitrix24TokenIsInvalidException
     * @throws Bitrix24WrongClientException
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public static function startTechnologicalPause(int $product_id): Product
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
            'ufCrm16Uchastok' => $historyProductUchastok, // участок
            'ufCrm16Dateandtime' => date('d-m-Y H:i:s'), // дата и время
        ];
        HistoryProduct::add($historyFields);
        return $product;
    }

    /**
     * @param int $product_id
     * @return Product
     * @throws Bitrix24ApiException
     * @throws Bitrix24EmptyResponseException
     * @throws Bitrix24Exception
     * @throws Bitrix24IoException
     * @throws Bitrix24MethodNotFoundException
     * @throws Bitrix24PaymentRequiredException
     * @throws Bitrix24PortalDeletedException
     * @throws Bitrix24PortalRenamedException
     * @throws Bitrix24SecurityException
     * @throws Bitrix24TokenIsExpiredException
     * @throws Bitrix24TokenIsInvalidException
     * @throws Bitrix24WrongClientException
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public static function endTechnologicalPause(int $product_id): Product
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
            'ufCrm16Uchastok' => $historyProductUchastok, // участок
            'ufCrm16Dateandtime' => date('d-m-Y H:i:s'), // дата и время
        ];
        HistoryProduct::add($historyFields);
        return $product;
    }

    /**
     * @param int $product_id
     * @return Product
     * @throws Bitrix24ApiException
     * @throws Bitrix24EmptyResponseException
     * @throws Bitrix24Exception
     * @throws Bitrix24IoException
     * @throws Bitrix24MethodNotFoundException
     * @throws Bitrix24PaymentRequiredException
     * @throws Bitrix24PortalDeletedException
     * @throws Bitrix24PortalRenamedException
     * @throws Bitrix24SecurityException
     * @throws Bitrix24TokenIsExpiredException
     * @throws Bitrix24TokenIsInvalidException
     * @throws Bitrix24WrongClientException
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public static function startPause(int $product_id): Product
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
            'ufCrm16Uchastok' => $historyProductUchastok, // участок
            'ufCrm16Dateandtime' => date('d-m-Y H:i:s'), // дата и время
        ];
        HistoryProduct::add($historyFields);
        return $product;
    }

    /**
     * @param int $product_id
     * @return Product
     * @throws Bitrix24ApiException
     * @throws Bitrix24EmptyResponseException
     * @throws Bitrix24Exception
     * @throws Bitrix24IoException
     * @throws Bitrix24MethodNotFoundException
     * @throws Bitrix24PaymentRequiredException
     * @throws Bitrix24PortalDeletedException
     * @throws Bitrix24PortalRenamedException
     * @throws Bitrix24SecurityException
     * @throws Bitrix24TokenIsExpiredException
     * @throws Bitrix24TokenIsInvalidException
     * @throws Bitrix24WrongClientException
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public static function endPause(int $product_id): Product
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
            'ufCrm16Uchastok' => $historyProductUchastok, // участок
            'ufCrm16Dateandtime' => date('d-m-Y H:i:s'), // дата и время
        ];
        HistoryProduct::add($historyFields);
        return $product;
    }
}
