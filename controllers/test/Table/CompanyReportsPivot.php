<?php

namespace app\models;

use Bitrix24\B24Object;
use wm\b24tools\b24Tools;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class CompanyReportsPivot extends \yii\base\Model
{
    public $employeId;
    public $companyId;

    public function rules()
    {
        return [
            [
                [
                    'employeId',
                    'companyId',
                ],
                'safe'
            ]
        ];
    }

    public function search($requestParams)
    {
        $this->load(ArrayHelper::getValue($requestParams, 'filter'), '');
        $query = ReportCompany::find()
            ->with([
                'company',
                'report',
                'user'
            ]);

        foreach ($this->attributes() as $value) {
            foreach ($this->{$value} as $item) {
                switch ($value) {
                    case 'companyId':
                        $query->andFilterCompare('company.' . $value, trim($item['value']), 'like');
                        break;
                }
            }
        }
        $companyReportModels = $query->all();
        Yii::warning($this->employeId, '$this->employeId');
        if ($this->employeId) {
            $companyReportModels = CompanyReportsPivot::getDataWithFilterEmployee($this->employeId[0][value], $companyReportModels);
        }
        return CompanyReportsPivot::getData($companyReportModels);
    }

    public static function getData($modelsCompanyReport)
    {
        $reportIds = array_unique(ArrayHelper::getColumn($modelsCompanyReport, 'report.id'));
        $companies = ArrayHelper::map(ArrayHelper::getColumn($modelsCompanyReport, 'company'), 'id', 'name');
        $bx24NotActiveUsersIds = self::getNotActiveUsers();
        $companiesWithNotActiveUsers = [];
        $data = [];
        foreach ($companies as $companyId => $company) {
            $row = [];
            $row['id'] = $companyId;
            $row['company'] = [
                "title" => $company,
                "type" => "openPath",
                "link" => "/crm/company/details/$companyId/"
            ];
            foreach ($reportIds as $reportId) {
                $row['code_' . $reportId] = null;
            }
            $data[$companyId] = $row;
        }
        foreach ($modelsCompanyReport as $modelCompanyReport) {
            if (in_array($modelCompanyReport->user->id, $bx24NotActiveUsersIds)) {//TODO
                $companiesWithNotActiveUsers[] = $modelCompanyReport->company->id;
            }

            $companyId = $modelCompanyReport->company->id;
            $reportColumn = 'code_' . $modelCompanyReport->report->id;
            $assigned = [
                "title" => $modelCompanyReport->user->name,
                "type" => "openApplication",
                'path' => 'mainForm', //Название страницы на фронте
                'entity' => 'company-reports',
                "id" => $modelCompanyReport->id,
                "action" =>  "update",
                "bx24_width" => 800,
                "updateOnCloseSlider" => true
            ];
            $data[$companyId][$reportColumn] = $assigned;
        }
//
        return [
            'grid' => array_values($data),
            'header' => [
//                'blocks' => [
//                    [
//                        'order' => 1,
//                        'items' => [
//                            [
//                                'order' => 1,
//                                'title' => 'Ответственный не назначен',
//                                'value' => count($companiesWithoutAssigned),
//                                'params' => [
//                                    'url' => [
//                                        'companyId' => [
//                                            ['operator' => '=', 'value' => $companiesWithoutAssigned]
//                                        ]
//                                    ]
////        'companyId=in[' . implode(',', $companiesWithoutAssigned) . ']'
//                                ],
//                                'type' => 'metric-filter',
//                            ],
//                            [
//                                'order' => 2,
//                                'title' => 'Назначен уволенный сотрудник',
//                                'value' => count($companiesWithNotActiveUsers),
//                                'params' => [
//                                    'url' => [
//                                        'companyId' => [
//                                            ['operator' => '=', 'value' => $companiesWithNotActiveUsers]
//                                        ]
//                                    ]
////                                        'companyId=in[' . implode(',', $companiesWithNotActiveUsers) . ']'
//                                ],
//                                'type' => 'metric-filter',
//                            ],
//                        ]
//                    ]
//                ]
            ],
            'footer' => []
        ];
    }


    public static function getDataWithFilterEmployee($employeIds, $companyReportModels)
    {
        if (!is_array($employeIds)) {
            $employeIds = [$employeIds];
        }
        $companies = [];
        foreach ($companyReportModels as $companyReportModel) {
            if (in_array($companyReportModel->user->id, $employeIds)) {
                $companies[] = $companyReportModel->company_id;
            }
        }
        $companies = array_unique($companies);
        $data = [];
        foreach ($companyReportModels as $key => $companyReportModel) {
            if (in_array($companyReportModel->company_id, $companies)) {
                $data[] = $companyReportModel;
            }
        }
        return $data;
    }

    private static function getCompaniesWithoutAssigned($data)
    {
        $result = [];
        foreach ($data as $row) {
            if (in_array(null, $row)) {
                $result[] = $row['id'];
            }
        }
        return array_unique($result);
    }

    private static function getNotActiveUsers()
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new B24Object($b24App);

        $request = $obB24->client->call('user.get', ['ACTIVE' => false]);
        $countCalls = (int)ceil($request['total'] / $obB24->client::MAX_BATCH_CALLS);
        $users = ArrayHelper::getValue($request, 'result');
        if (count($users) != $request['total']) {
            for ($i = 1; $i < $countCalls; $i++) {
                $obB24->client->addBatchCall(
                    'user.get',
                    array_merge([], ['start' => $obB24->client::MAX_BATCH_CALLS * $i]),
                    function ($result) use (&$users) {
                        $users = array_merge($users, ArrayHelper::getValue($result, 'result.ID'));
                    }
                );
            }
        }
        $obB24->client->processBatchCalls();
        return ArrayHelper::getColumn($users, 'ID');
    }
}
