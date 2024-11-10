<?php

namespace app\controllers;

use app\models\ReportCompany;
use app\models\ReportCompanySearch;
use wm\yii\rest\ActiveRestController;
use yii\helpers\Url;

class ReportCompanyController extends ActiveRestController
{
    public $modelClass = ReportCompany::class;
    public $modelClassSearch = ReportCompanySearch::class;

//    public function actionGetButtonAdd()
//    {
//        $parentId = \Yii::$app->request->post('parentId');
//        return [
//            'title' => 'Добавить все отчеты',
//            'params' => [
//                'handler' => '/contacts/popup-form?companyId='. $parentId,
//                "type" => "popup",
//                'updateOnCloseSlider' => true,
//                "popup" => [
//                    "width" => 400,
//                    "height" => 450,
//                    "title" => "Добавить все отчеты",
//                    "body" => [
//                        'text' => 'Вы действительно хотите добавить все отчеты к данной компании с ответственным по умолчанию',
////                        "form" => [
//////                                    "fields" => [],
//////                                    'validationRules' => []
////                        ]
//                    ],
//                    "buttons" => [
//                        "success" => "ОК",
//                        "cancel" => "Отмена"
//                    ]
//                ],
//            ],
//        ];
//    }

    public function actionGetFormFields()
    {
        return [
            [
                'type' => 'select',
                'name' => 'user_id',
                'label' => 'Ответственный',
                'fieldParams' => [
                    'dataUrl' => Url::toRoute('/company-reports-pivot-change-assigned/assigned-list', 'https'),
                    'remoteMode' => true
                ]
            ],
        ];
    }
}
