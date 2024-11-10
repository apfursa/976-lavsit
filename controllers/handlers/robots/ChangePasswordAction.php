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

// Изменить пароль (СП Мастер)

/**
 *
 */
class ChangePasswordAction extends Action
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
     * @throws \Exception
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
        $obB24 = new \Bitrix24\Bizproc\Event($b24App);
        $obB24->send($event_token, $returnValues);
        return '';
    }

    /**
     * @param array<string, mixed> $properties
     * @param Bitrix24|null $b24App
     * @return array<string, string>
     * @throws Exception
     * @throws \yii\db\Exception
     * @throws \Exception
     */
    public function logicForThisRobot(array $properties, Bitrix24 $b24App = null): array
    {
        $userId = ArrayHelper::getValue($properties, 'userId'); // id  мастера
        Yii::warning($userId, '$userId');
        $user = User::find()->where(['id' => $userId])->one();
        $newPass = Yii::$app->security->generateRandomString(8);
        $newPassHash = Yii::$app->security->generatePasswordHash($newPass);
        $user->password = $newPassHash;
        $user->save();
        return [
            'newPass' => $newPass
        ];
    }
}
