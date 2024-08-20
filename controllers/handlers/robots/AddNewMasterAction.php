<?php

namespace app\controllers\handlers\robots;

use wm\admin\models\User;
use yii\base\Action;
use Yii;
use yii\helpers\ArrayHelper;

// Добавить нового мастера (СП Мастер)
class AddNewMasterAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $auth = $request->post('auth');
        $event_token = $request->post('event_token');
        $properties = $request->post('properties');
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromUser($auth);
        $returnValues = $this->logicForThisRobot($properties, $b24App);
        $obB24 = new \Bitrix24\Bizproc\Event($b24App);
        $obB24->send($event_token, $returnValues);
        return '';
    }

    public function logicForThisRobot($properties, $b24App = null)
    {
        $surname = ArrayHelper::getValue($properties, 'surname'); // Фамилия
        $name = ArrayHelper::getValue($properties, 'name'); // Имя
        $patronymic = ArrayHelper::getValue($properties, 'patronymic'); // Отчество
        $login = $surname. '@' . $name;
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
