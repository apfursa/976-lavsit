<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use wm\admin\models\settings\Agents;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CronController extends Controller
{

    /**
     * @return int
     */
    public function actionUpdateConnect()
    {
        $portalNames = (new \yii\db\Query())
            ->select('PORTAL')
            ->from(Yii::$app->params['b24PortalTable'])
            ->all();
        $component = new \wm\b24tools\b24Tools();
        foreach ($portalNames as $portalName){
            $component->connectFromAdmin($portalName);
        }

        return ExitCode::OK;
    }

    public function actionAgentsRun()
    {
        $date = date('Y-m-d H:i:s');
        Yii::warning($date, '$date');
        Agents::shedulRun();
        return ExitCode::OK;
    }
}
