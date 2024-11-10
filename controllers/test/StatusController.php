<?php

namespace app\controllers\test;

use Yii;

class StatusController extends \wm\admin\controllers\RestController
{
    public function actionCrm_status_list()
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $request = $obB24->client->call(
            'crm.status.list',
            [
                'filter' => [
                    'ENTITY_ID' => 'DYNAMIC_187_STAGE_14'
                ]
            ]
        );
        return $request;
    }
}
