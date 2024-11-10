<?php

namespace app\controllers\test;

use Yii;

class UrokController extends \wm\admin\controllers\RestController
{
    public function actionUrok1()
    {
        $date = '2024-08-30';
        $time = '15:32:00';
        $d1 = strtotime($date);
        $t1 = strtotime($time);
        $res = date('d-m-Y ' . $time, $d1);


        return $res;
    }
}
