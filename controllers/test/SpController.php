<?php

namespace app\controllers\test;

use Yii;
use yii\helpers\ArrayHelper;

class SpController extends \wm\admin\controllers\RestController
{
    public function actionCrm_item_get($typeId, $id)
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $request = $obB24->client->call(
            'crm.item.get',
            [
                'id' => $id,
                'entityTypeId' => $typeId
            ]
        );
        return $request;
    }

    public function actionCrm_item_list($typeId)
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $request = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => $typeId,
                'order' => ['id' => 'ASC'],
                'filter' => [
                    '>id' => 0,
                    'ufCrm18UshelTime' => ''
                ]
            ]
        );
        return $request;
    }

    public function actionCrm_item_list_h()
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $history = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => 1042, //СП "История изделия"
                'order' => ['id' => 'DESC'], // сортировать по убыванию
                //                'filter' => ['parentId187' => 9788,],
            ]
        )['result']['items'];
        return $history;
    }

    public function actionCrm_item_fields($typeId)
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $request = $obB24->client->call(
            'crm.item.fields',
            [
                'entityTypeId' => $typeId
            ]
        );
        return $request;
    }

    public function actionFillField()
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $request = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => 187,
                'order' => ['id' => 'ASC'], // по возрастанию
                'filter' => [
                    'categoryId' => 14,
                    '>id' => 0,
                    'ufCrm8Idinproduction' => ''
                ]
            ]
        );
        $count = ArrayHelper::getValue($request, 'total');
        while ($count > 0) {
            $maxId = max(ArrayHelper::getColumn($request['result']['items'], 'id'));
            $component = new \wm\b24tools\b24Tools();
            $b24App = $component->connectFromAdmin();
            $obB24 = new \Bitrix24\B24Object($b24App);
            foreach ($request['result']['items'] as $item) {
                $obB24->client->addBatchCall(
                    'crm.item.update',
                    [
                        'entityTypeId' => 187,
                        'id' => $item['id'],
                        'fields' => [
                            'ufCrm8Idinproduction' => $item['id'] // ID в воронке Производство
                        ]
                    ]
                );
            }
            $obB24->client->processBatchCalls();
            sleep(5);
            $component = new \wm\b24tools\b24Tools();
            $b24App = $component->connectFromAdmin();
            $obB24 = new \Bitrix24\B24Object($b24App);
            $request = $obB24->client->call(
                'crm.item.list',
                [
                    'entityTypeId' => 187,
                    'order' => ['id' => 'ASC'], // по возрастанию
                    'filter' => [
                        'categoryId' => 14,
                        '>id' => $maxId,
                        'ufCrm8Idinproduction' => ''
                    ]
                ]
            );
            $count = ArrayHelper::getValue($request, 'total');
        }
        return 'ok';
    }

    public function actionGetEntityByFilter()
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $request = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => 187,
                'order' => ['id' => 'ASC'], // по возрастанию
                'filter' => [
                    'categoryId' => 18,
                    '!=ufCrm8Idinproduction' => ''
                ]
            ]
        );
        return $request;
    }
}
