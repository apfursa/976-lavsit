<?php

namespace app\models\productManagement;

use yii\helpers\ArrayHelper;
use yii\base\Model;
use wm\yii\db\ActiveRecord;
use Yii;

// Мастер

/**
 * Class Master3
 * @property string $name
 * @property string $last_name
 * @property string $sur_name
 * @property string $login
 * @property string $pass
 * @property string $usersId
 * @property string $subdivisionId
 * @property string $subdivision
 * @property string $date_create
 * @package app\controllers\productManagement
 */
class Master3 extends Model
{
    /**
     *
     */
    public const ENTITY_TYPE_ID = 1036;

    /**
     * @var int
     */
    public int $id;
    /**
     * @var int
     */
    public int $ufCrm14Workshop; // участок
    /**
     * @var int
     */
    public int $ufCrm14Status; // принят/уволен

    /**
     * @param int $usersId
     * @return array<string, mixed>
     * @throws \Bitrix24\Exceptions\Bitrix24ApiException
     * @throws \Bitrix24\Exceptions\Bitrix24EmptyResponseException
     * @throws \Bitrix24\Exceptions\Bitrix24Exception
     * @throws \Bitrix24\Exceptions\Bitrix24IoException
     * @throws \Bitrix24\Exceptions\Bitrix24MethodNotFoundException
     * @throws \Bitrix24\Exceptions\Bitrix24PaymentRequiredException
     * @throws \Bitrix24\Exceptions\Bitrix24PortalDeletedException
     * @throws \Bitrix24\Exceptions\Bitrix24PortalRenamedException
     * @throws \Bitrix24\Exceptions\Bitrix24SecurityException
     * @throws \Bitrix24\Exceptions\Bitrix24TokenIsExpiredException
     * @throws \Bitrix24\Exceptions\Bitrix24TokenIsInvalidException
     * @throws \Bitrix24\Exceptions\Bitrix24WrongClientException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public static function getMasterByUserId(int $usersId): array
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'crm.item.list',
            [
                'entityTypeId' => self::ENTITY_TYPE_ID,
                'filter' => ['ufCrm14Userid' => $usersId]
            ]
        );
        return $answerB24['result']['items'][0];
    }
}
