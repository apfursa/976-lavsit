<?php

namespace app\models;

use wm\yii\helpers\ArrayHelper;

/**
 * ReportCompanySearch represents the model behind the search form of `app\models\ReportCompany`.
 */
class ReportCompanySearch extends ReportCompany
{
    public $parentId;

    public function rules()
    {
        return [
            [
                array_merge(
                    $this->attributes(),
                    ['parentId']
                ),
                'safe'
            ]
        ];
    }

    public function prepareSearchQuery($query, $requestParams)
    {

        $this->load(ArrayHelper::getValue($requestParams, 'filter'), '');
        \Yii::warning($this->parentId, '$this');

        if (!$this->validate()) {
            $query->where('0=1');
            return $query;
        }
        foreach ($this->attributes() as $value) {
            \Yii::warning($value, '$value');
            foreach ($this->{$value} as $item) {
                $query->andFilterCompare($value, $item['value'], $item['operator']);
            }
        }

        if ($this->parentId) {
            foreach ($this->parentId as $item) {
                $query->andFilterCompare('company_id', $item['value'], $item['operator']);
            }
        }
        \Yii::warning(ArrayHelper::toArray($query));
        return $query;
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function rules()
//    {
//        return [
//            [[/*'id', */'company_id', 'report_id', 'user_id'], 'integer'],
//            [['id'], 'string'],
//        ];
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function scenarios()
//    {
//        // bypass scenarios() implementation in the parent class
//        return Model::scenarios();
//    }
//
//    /**
//     * Creates data provider instance with search query applied
//     *
//     * @param array $params
//     *
//     * @return ActiveDataProvider
//     */
//    public function search($params)
//    {
//        $query = ReportCompany::find();
//
//        // add conditions that should always apply here
//
//        $dataProvider = new ActiveDataProvider([
//            'pagination' => false,
//            'query' => $query,
//        ]);
//
//        $this->load($params);
//        Yii::warning($this->id, '$this->id');
//        if (!$this->validate()) {
//            Yii::warning('$this->validate');
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }
//
//        // grid filtering conditions
//        $query->andFilterWhere([
////            'id' => $this->id,
//            'company_id' => $this->company_id,
//            'report_id' => $this->report_id,
//            'user_id' => $this->user_id,
//        ]);
//        $query->andFilterCompare('id', $this->id);
//
//        return $dataProvider;
//    }
}
