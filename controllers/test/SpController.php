<?php

namespace app\controllers\test;

use Yii;

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
//                'order' => ['id'=>'DESC'],
                'filter' => [
                    'id' => 9980
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
                'filter' => ['parentId187' => 9788, ],
            ]
        )['result']['items'][0];
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
}


