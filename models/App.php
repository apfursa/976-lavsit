<?php

namespace app\models;

use Bitrix24\B24Object;
use Bitrix24\Im\Im;
use wm\admin\models\User;
use wm\b24tools\b24Tools;
use yii\helpers\Url;
use Bitrix24\User\User as B24User;

class App extends \yii\base\Model
{
    public static function install()
    {
//        self::addDeleteEvent();
//        self::addInstallEvent();
    }

    private static function addInstallEvent()
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new B24User($b24App);
        $obB24->client->call('event.bind', [
            'event' => 'OnAppInstall',
            'handler' => Url::toRoute('/events/app/install', 'https')
        ]);
    }

    private static function addDeleteEvent()
    {
        $component = new b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new B24User($b24App);
        $obB24->client->call('event.bind', [
            'event' => 'OnAppUninstall',
            'handler' => Url::toRoute('/events/app/delete', 'https')
        ]);
    }
}
