<?php
namespace app\controllers;

use app\models\CompanyReportsPivot;
use app\models\CompanyReportsPivotChangeAssigned;
use wm\admin\models\Settings;
use wm\admin\models\User;
use Yii;
use app\models\ReportCompany;
use app\models\ReportCompanyGroup;
use app\models\Report;
use yii\filters\auth\CompositeAuth;
use yii\helpers\ArrayHelper;
use wm\yii\filters\auth\HttpBearerAuth;
use app\models\Company;

class ReportCompanyGroupController extends \wm\admin\controllers\RestController
{
    public $modelClass = ReportCompanyGroup::class;

    public function actionData()
    {
        $request = \yii::$app->request;
        $model = new CompanyReportsPivot();
        Yii::warning($request->getBodyParams());
        return $model
            ->search(
                array_merge
                (
                    $request->getBodyParams(),
                    $request->getQueryParams()
                )
            );


//        $filter = \Yii::$app->request->post('filter');
//        $companyId = $filter['companyId'][0]['value'];
//        $employeeId = $filter['employeeId'][0]['value'];
//        $where = [];
//        if ($companyId) {
////            if (preg_match("/^(in\[.*\])/", $companyId)) {
////                $companyId = explode(',', mb_substr($companyId, 3, -1));
////            } else {
////                $companyId = [$companyId];
////            }
//            $where['companyId'] = $companyId;
//        }
//        $companyReportModels = ReportCompany::find()
//            ->with([
//                'company',
//                'report',
//                'user'
//            ])
//            ->where($where)
//            ->all();
//        if ($employeeId) {
//            $companyReportModels = CompanyReportsPivot::getDataWithFilterEmployee($employeeId, $companyReportModels);
//        }
//        return CompanyReportsPivot::getData($companyReportModels);
    }

    public function actionGridActions()
    {
        return [
            [
                'label' => 'Сменить ответственного',
                'handler' => '/company-reports/change-assigned',
                'params' => [
                    'updateOnCloseSlider' => true,
                    "popup" => [
                        "width" => 500,
                        "height" => 450,
                        "title" => "Сменить ответственного",
                        "body" => [
                            "form" => [
                                "fields" => CompanyReportsPivotChangeAssigned::formFields(),
                                'validationRules' => CompanyReportsPivotChangeAssigned::getRestRules()
                            ]
                        ],
                        "buttons" => [
                            "success" => "ОК",
                            "cancel" => "Отмена"
                        ]
                    ]
                ]
            ]
        ];
    }


    public function actionIndex($categoryId = null)
    {
        $queryParams = Yii::$app->getRequest()->getQueryParams();

        $userBxId = User::find()->where(['id' => Yii::$app->user->getId()])->select(['b24_user_id'])->asArray()->one()['b24_user_id'];
        $adminUsers = explode(",", Settings::getParametrByName('isAdmin'));
        $isAdmin = false;
        if (array_search($userBxId, $adminUsers) !== false) {
            $isAdmin = true;
        }

        return ReportCompanyGroup::getData($categoryId, null, $userBxId, $isAdmin, $queryParams['user_id']);
    }

    public function actionView($companyId, $categoryId = null)
    {
        $userBxId = User::find()->where(['id' => Yii::$app->user->getId()])->select(['b24_user_id'])->asArray()->one()['b24_user_id'];
        $adminUsers = explode(",", Settings::getParametrByName('isAdmin'));
        $isAdmin = false;
        if (array_search($userBxId, $adminUsers) !== false) {
            $isAdmin = true;
        }
        return ReportCompanyGroup::getData($categoryId, $companyId, $userBxId, $isAdmin)[0];
    }

    public function actionUpdate($companyId, $categoryId = null)
    {
        $company = Company::findOne($companyId);

        if ($company === null) {
            $company = Company::createByBx24Id($companyId);
        }

        $request = Yii::$app->request;
        Yii::warning('actionUpdate', 'action');
        $data = [
            'companyId' => $companyId,
            'form' => $request->post(),
//            'oldResponsi ble' => $request->post('oldResponsible'),
//            'newResponsible' => $request->post('newResponsible'),
//            'companyIds' => $request->post('companyIds'),
//            'categoryId'=>$request->post('categoryId')
        ];
        ReportCompanyGroup::update($data);
        return true;
    }

    public function actionEditResponsible()
    {
        $request = Yii::$app->request;
        Yii::warning('actionEditResponsible', 'action');
        $data = [
            'oldResponsible' => $request->post('oldResponsible'),
            'newResponsible' => $request->post('newResponsible'),
            'companyIds' => $request->post('companyIds'),
            'categoryId' => $request->post('categoryId')
        ];
        ReportCompanyGroup::editResponsible($data);
        return true;
    }

    public function actionEditFunctional()
    {
        $request = Yii::$app->request;
        Yii::warning('actionEditFunctional', 'action');
        $data = [
            'oldResponsible' => $request->post('oldResponsible'),
            'newResponsible' => $request->post('newResponsible'),
            'companyIds' => $request->post('companyIds'),
            'reportId' => $request->post('reportId')
        ];
        ReportCompanyGroup::editFunctional($data);
        return true;
    }

//    public function actionData()
//    {
//        $res = ReportCompany::getGroupCompanyData();
//        return 'Hello Filipp';
//    }

    public function actionSchema($categoryId)
    {
        Yii::warning(Yii::$app->user->id, 'Yii:user');
        $model = new $this->modelClass($categoryId);
        return $model->schema;
    }

    public function actionValidation()
    {
        $model = new $this->modelClass();
        return $model->restRules;
    }
}
