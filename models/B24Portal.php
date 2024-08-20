<?php

namespace app\models;

/**
 * Class B24Portal
 * @package app\models
 * @property string $applicationToken
 */
class B24Portal extends \wm\yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'admin_b24portal';
    }
}
