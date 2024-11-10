<?php

namespace app\controllers\handlers\robots;

use Bitrix24\Bitrix24;
use Bitrix24\Exceptions\Bitrix24ApiException;
use Bitrix24\Exceptions\Bitrix24EmptyResponseException;
use Bitrix24\Exceptions\Bitrix24Exception;
use Bitrix24\Exceptions\Bitrix24IoException;
use Bitrix24\Exceptions\Bitrix24MethodNotFoundException;
use Bitrix24\Exceptions\Bitrix24PaymentRequiredException;
use Bitrix24\Exceptions\Bitrix24PortalDeletedException;
use Bitrix24\Exceptions\Bitrix24SecurityException;
use Bitrix24\Exceptions\Bitrix24TokenIsExpiredException;
use Bitrix24\Exceptions\Bitrix24TokenIsInvalidException;
use Bitrix24\Exceptions\Bitrix24WrongClientException;
use wm\admin\models\User;
use yii\base\Action;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

// Добавить нового мастера (СП Мастер)

/**
 *
 */
class AddNewMasterAction extends Action
{
    /**
     * @return string
     * @throws Bitrix24ApiException
     * @throws Bitrix24EmptyResponseException
     * @throws Bitrix24Exception
     * @throws Bitrix24IoException
     * @throws Bitrix24MethodNotFoundException
     * @throws Bitrix24PaymentRequiredException
     * @throws Bitrix24PortalDeletedException
     * @throws Bitrix24SecurityException
     * @throws Bitrix24TokenIsExpiredException
     * @throws Bitrix24TokenIsInvalidException
     * @throws Bitrix24WrongClientException
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function run(): string
    {
        $request = Yii::$app->request;
        $auth = $request->post('auth');
        $event_token = $request->post('event_token');
        $properties = $request->post('properties');
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromUser($auth);
        $returnValues = [];
        try {
            $returnValues = $this->logicForThisRobot($properties, $b24App);
        } catch (\yii\db\Exception|Exception|\Exception $e) {
        }
//        $returnValues = $this->logicForThisRobot($properties, $b24App);
        $obB24 = new \Bitrix24\Bizproc\Event($b24App);
        $obB24->send($event_token, $returnValues);
        return '';
    }

    /**
     * @param array<string, mixed> $properties
     * @param Bitrix24|null $b24App
     * @return array<string, mixed>
     * @throws Exception
     * @throws \yii\db\Exception
     * @throws \Exception
     */
    public function logicForThisRobot(array $properties, Bitrix24 $b24App = null): array
    {
        $surname = ArrayHelper::getValue($properties, 'surname'); // Фамилия
        $name = ArrayHelper::getValue($properties, 'name'); // Имя
        $patronymic = ArrayHelper::getValue($properties, 'patronymic'); // Отчество
        $login = $surname . '@' . $name;
        $newPass = Yii::$app->security->generateRandomString(8);
        $passHash = Yii::$app->security->generatePasswordHash($newPass);
        $user = new User();
        $user->password = $passHash;
        $user->username = $login;
        $user->last_name = $surname;
        $user->name = $name;
        $user->save();
        $userId = User::find()->where(['password' => $passHash])->andWhere(['username' => $login])->one()->id;
        return [
            'login' => $login,
            'password' => $newPass,
            'userId' => $userId
        ];
    }
}
