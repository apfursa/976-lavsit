<?php

namespace app\controllers\handlers\robots;

use wm\admin\models\User;
use yii\base\Action;
use Yii;
use yii\helpers\ArrayHelper;

// Изменить пароль (СП Мастер)
class ChangePasswordAction extends Action
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
        $userId = ArrayHelper::getValue($properties, 'userId'); // id  мастера
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
