<?php

namespace app\models;

use wm\yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Class StockSearch
 * @package app\models\economy
 */
class DemoSearch extends Demo
{
    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [
                array_merge(
                    array_keys($this->attributes),
                    []
                ),
                'safe'
            ]
        ];
    }


    /**
     * @param ActiveQuery $query
     * @param array $requestParams
     * @return mixed
     * @throws \Exception
     */
    public function prepareSearchQuery($query, $requestParams)
    {
        $this->load(ArrayHelper::getValue($requestParams, 'filter'), '');
        if (!$this->validate()) {
            $query->where('0=1');
            return $query;
        }
        foreach ($this->attributes() as $value) {
            foreach ($this->{$value} as $item) {
                $query->andFilterCompare($value, $item['value'], $item['operator']);
            }
        }
        return $query;
    }
}
