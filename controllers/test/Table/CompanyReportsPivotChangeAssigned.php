<?php

namespace app\models;

use yii\base\Model;
use yii\helpers\Url;

class CompanyReportsPivotChangeAssigned extends Model
{
    public function rules()
    {
        return [
            [['reportId', 'currentAssignedId', 'newAssignedId'], 'required'],
            [['currentAssignedId', 'newAssignedId'], 'integer'],
            [['reportId'], 'each', 'rule' => ['integer']]
        ];
    }

    public static function getRestRules()
    {
        $res = [];
        $model = new static();
        $rules = $model->rules();
        foreach ($rules as $value) {
            $temp = [];
            $temp['fields'] = array_shift($value);
            $temp['type'] = array_shift($value);
            $temp['rules'] = $value;
            $res[] = $temp;
        }
        return $res;
    }

    public static function formFields()
    {
        return [
            ['type' => 'select', 'name' => 'currentAssignedId', 'label' => 'Текущий ответственный', 'fieldParams' => [
                'dataUrl' => Url::toRoute('/company-reports-pivot-change-assigned/assigned-list', 'https'),
                'remoteMode' => true
            ]],
            ['type' => 'select', 'name' => 'newAssignedId', 'label' => 'Новый ответственный', 'fieldParams' => [
                'dataUrl' => Url::toRoute('/company-reports-pivot-change-assigned/assigned-list', 'https'),
                'remoteMode' => true
            ]],
            ['type' => 'select', 'name' => 'reportId', 'label' => 'Отчёт', 'fieldParams' => [
                'dataUrl' => Url::toRoute('/company-reports-pivot-change-assigned/reports-list', 'https'),
                'multiple' => true,
                'closeOnSelect' => false,
                'remoteMode' => true
            ]],
        ];//
    }
}