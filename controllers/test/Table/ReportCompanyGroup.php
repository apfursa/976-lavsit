<?php
namespace app\models;

use Yii;
use yii\base\DynamicModel;
use app\models\ReportCompany;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "report_company".
 *
 * @property int $id
 * @property int $company_id
 * @property int $report_id
 * @property int $user_id
 *
 * @property Report $report
 * @property ReportUser $user
 */
class ReportCompanyGroup extends DynamicModel
{
    /**
     * {@inheritdoc}
     */
//    public static function tableName()
//    {
//        return 'report_company';
//    }

    /**
     * {@inheritdoc}
     */
    function __construct($categoryId = 1)
    {
        $fieldsName = ['company_name'];
        $reportIds = ArrayHelper::getColumn(Report::find()->select('id')->where(['category_id' => $categoryId])->asArray()->all(), 'id');
        array_walk($reportIds, function(&$item) {
            $item = 'code_' . $item;
        });
        $fields = array_merge($fieldsName, $reportIds);
        parent::__construct($fieldsName);
        $this->categoryId = $categoryId;
    }

    private $categoryId;

    public function rules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $fieldsName = ['company_name' => 'Компания'];
        $reportName_ = Report::find()->where(['category_id' => $this->categoryId])->asArray()->all();
        $reportName = [];
        foreach ($reportName_ as $value) {            
            $reportName[] = ['id' => 'code_'.$value['id'], 'name' => $value['name']];
        }
        $fields = array_merge($fieldsName, ArrayHelper::map($reportName, 'id', 'name'));
        return $fields;
    }

    public function getRestRules()
    {
        $res = [];
        $rules = $this->rules();
        foreach ($rules as $value) {
            $temp = [];
            $temp['fields'] = array_shift($value);
            $temp['type'] = array_shift($value);
            $temp['rules'] = $value;
            $res[] = $temp;
        }
        return $res;
    }

    public function getSchema()
    {
        $res = [];
        $attributeLabels = $this->attributeLabels();
        foreach ($attributeLabels as $key => $value) {
            $temp = [];
            $temp['id'] = $key;
            $temp['title'] = $value;
            $res[] = $temp;
        }
        return $res;
    }

    public static function getData( $companyId = null, $userId = null, $isAdmin = false, $filterUserId = null)
    {
//        $isAdmin = true;
        //Yii::warning(ReportCompany::find()->with(['company', 'user'])->asArray()->all());
        $whereReport  = [];
        //$where['company_id'] =  2;
//        if($categoryId){
//           $whereReport['category_id'] =  $categoryId;
//        }
        $report = ArrayHelper::getColumn(Report::find()->where($whereReport)->all(), 'id');
        $where  = [
            'report_id' => $report
            ];
        if (is_numeric($filterUserId)) {
            $where['user_id'] = $filterUserId;
        } elseif (preg_match('/^(in\[.*])/', $filterUserId, $matches)) {
            $value = explode(',', mb_substr($filterUserId, 3, -1));
            $where['user_id'] = $value;
        }

        //$where['company_id'] =  2;
        if($companyId){
           $where['company_id'] =  $companyId;
        }
//        if($categoryId){
            $category = ReportCategory::find()->where(['id' => $categoryId])->one();
            $reports = $category->reports;
            $where['report_id'] =  ArrayHelper::getColumn($reports, 'id');
//        }
        Yii::warning($where, 'where');
        $reportCompany = ReportCompany::find()->with(['company', 'user'])->where($where)->asArray()->all();
        $company = array_unique(ArrayHelper::getColumn($reportCompany, 'company_id'));
        $reportCompany = ReportCompany::find()->with(['company', 'user'])->where(['company_id' => $company])->asArray()->all();
        $res = array_values(static::convertData($reportCompany, $userId, $isAdmin));
        $res = static::afterGetData($res);
        
        
        //$res = ArrayHelper::map($reports, 'report_id', 'user_id', 'company_id');
        return $res;
    }
    
    protected static function convertData($data, $userId, $isAdmin){        
        Yii::warning($isAdmin, '$isAdmin convertData');
        $res = [];
        $companyAccess = [];
        foreach ($data as $value){
            if($isAdmin){
                $companyAccess[$value['company_id']] = true;
                $res[$value['company_id']]['company_name'] = ['title' => $value['company']['name'], 'link' => $value['company_id']];
                $res[$value['company_id']]['code_'.$value['report_id']] = ['title' => $value['user']['name'], 'link' => $value['user_id']];
            }else{
                if(!ArrayHelper::getValue($companyAccess, $value['company_id'])&&$value['user_id'] == $userId){
                    $companyAccess[$value['company_id']] = true;
                }                
                $res[$value['company_id']]['company_name'] = ['title' => $value['company']['name'], 'link' => $value['company_id']];
                $res[$value['company_id']]['code_'.$value['report_id']] = ['title' => $value['user']['name'], 'link' => $value['user_id']];
            }
            
        }
        Yii::warning($companyAccess, '$companyAccess convertData');
        $res_ = [];
        foreach ($res as $key => $value) {
            if(ArrayHelper::getValue($companyAccess, $key)){
                $res_[$key] = $value;
            }
        }
        return $res_;
    }
    
    protected static function afterGetData($data){
        $res = [];
        
        foreach ($data as $value){
           $temp = $value;
            if(ArrayHelper::getValue($value, 'company_name')){
                $temp['company_name']['id'] = $value['company_name']['link'];
                $temp['company_name']['title'] = $value['company_name']['title'];
                //if($value['company_name']['link']>=1){
                    $temp['company_name']['link'] = '/crm/company/details/'.$value['company_name']['link'].'/';
                    $temp['company_name']['type'] = 'openPath';
                //}
                $res[] = $temp;
            }
            
        }
        return $res;
    }
    
    public static function editResponsible($data){
        
        //Yii::warning(ReportCompany::find()->with(['company', 'user'])->asArray()->all());
        $whereReport  = [];
        //$where['company_id'] =  2;
        if($data['categoryId']){
           $whereReport['category_id'] =  $data['categoryId'];
        }
        
        Yii::warning($whereReport, '$whereReport');
        $report = ArrayHelper::getColumn(Report::find()->where($whereReport)->all(), 'id');
        Yii::warning($report, '$report');
        
        
        $where  = ['company_id' => $data['companyIds'], 'user_id' => $data['oldResponsible'], 'report_id' => $report];
        
        //$where['company_id'] =  2;
//        if($companyId){
//           $where['company_id'] =  $companyId;
//        }
        $reportCompany = ReportCompany::find()->where($where)->all();
        Yii::warning(ArrayHelper::toArray($reportCompany), '$reportCompany');
        foreach ($reportCompany as $value) {
            $value->user_id = $data['newResponsible'];
            $value->save();
            Yii::warning($value->errors, '$value->errors');
        }
        
    }
    
    public static function editFunctional($data){
        $where  = ['company_id' => $data['companyIds'], 'user_id' => $data['oldResponsible'], 'report_id' => $data['reportId']];
        $reportCompany = ReportCompany::find()->where($where)->all();
        Yii::warning(ArrayHelper::toArray($reportCompany), '$reportCompany');
        foreach ($reportCompany as $value) {
            $value->user_id = $data['newResponsible'];
            $value->save();
            Yii::warning($value->errors, '$value->errors');
        }
        
    }
    
    public static function update($data){
        Yii::warning($data, '$data');
    foreach ($data['form'] as $key => $value) {
        $reportCompany = ReportCompany::find()->where(['company_id'=>$data['companyId'], 'report_id' => substr($key, 5)])->one();
        Yii::warning(ArrayHelper::toArray($reportCompany), 'ReportCompany');
        $reportCompany->user_id = $value['link'];
        $reportCompany->save();
    }
        
//        $where  = ['company_id' => $data['companyIds'], 'user_id' => $data['oldResponsible'], 'report_id' => $data['reportId']];
//        $reportCompany = ReportCompany::find()->where($where)->all();
//        Yii::warning(ArrayHelper::toArray($reportCompany), '$reportCompany');
//        foreach ($reportCompany as $value) {
//            $value->user_id = $data['newResponsible'];
//            $value->save();
//            Yii::warning($value->errors, '$value->errors');
//        }
        
    }
}
