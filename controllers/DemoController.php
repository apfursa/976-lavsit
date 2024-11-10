<?php

namespace app\controllers;

use app\models\Demo;
use app\models\DemoSearch;
use wm\yii\rest\ActiveRestController;

/**
 *
 */
class DemoController extends ActiveRestController
{
    /**
     * @var string
     */
    public $modelClass = Demo::class;
    /**
     * @var string
     */
    public $modelClassSearch = DemoSearch::class;
}
