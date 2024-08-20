<?php

namespace app\controllers\handlers\events;

use app\models\B24Portal;
use Yii;

class AppController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionInstall()
    {
        $auth = Yii::$app->request->post('auth');
        $applicationToken = $auth['application_token'];
        $portalName = $auth['domain'];
        $model = B24Portal::find()->where(['PORTAL' => $portalName])->one();

        if ($model) {
            $model->applicationToken = $applicationToken;
            $model->save();
        }
    }

    public function actionDelete()
    {
        $auth = Yii::$app->request->post('auth');
        $portalName = $auth['domain'];
        $model = B24Portal::find()->where(['PORTAL' => $portalName])->one();
        $applicationToken = $auth['application_token'];

        if ($model->applicationToken == $applicationToken) {
        }
    }
}
