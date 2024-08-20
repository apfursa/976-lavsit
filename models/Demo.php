<?php

namespace app\models;

/**
 * This is the model class for table "demo".
 *
 * @property int $id
 * @property string $title
 * @property int $value
 * @property int $userId
 */
class Demo extends \wm\yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'demo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'value', 'userId'], 'required'],
            [['value', 'userId'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }
}

