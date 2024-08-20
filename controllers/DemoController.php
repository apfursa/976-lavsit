<?php

namespace app\controllers;

use app\models\Demo;
use app\models\DemoSearch;
use wm\yii\rest\ActiveRestController;

class DemoController extends ActiveRestController
{
    public $modelClass = Demo::class;
    public $modelClassSearch = DemoSearch::class;
}
