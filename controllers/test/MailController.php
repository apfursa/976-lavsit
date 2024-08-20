<?php

namespace app\controllers\test;

use app\models\SendMail;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class MailController extends Controller
{
    public function actionSendMail()
    {
        $model = new SendMail();


        if ($a = $model->sendEmail()) {
            file_put_contents('log11.txt', "$a\n", FILE_APPEND);
            Yii::$app->session->setFlash('success', 'Проверьте свою электронную почту для получения дальнейших инструкций.');
            return $this->goHome();
        } else {
            Yii::$app->session->setFlash('error', 'Извините, мы не можем сбросить пароль для предоставленной электронной почты.');
        }
        return '';
    }
}
