<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'ru-RU',    
    'components' => [
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'forceCopy' => true,
        ],
        'request' => [
            'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			],		
			'baseUrl'=> '',
            // !!! Сгенерировать и вставить ключ
            'cookieValidationKey' => 'H1S7r-4sYu37UyXcD3jv9_S-2T8N3BwC',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'wm\admin\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'logTable' => 'log',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'formatter' => [],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'session'      => [
            'class'        => 'yii\web\CacheSession',
            'name'         => 'mysession',
            'timeout'      => 86400,
            'useCookies'   => true,
            'cookieParams' => [
                'httponly' => true,
                'secure' => true,
                'sameSite' => 'None',
            ],
        ],
        'queue' => [
            'class' => \yii\queue\file\Queue::class,
            'path' => '@runtime/queue',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
        'generators' => [
            'reactmodel' => [
                'class' => 'wm\admin\models\gii\model\Generator',
                'templates' => [
                    'reactModel' => '@app/vendor/webmens-ru/yii2-admin/src/models/gii/model/react',
                ]
            ],
            'reactcrud' => [
                'class' => 'wm\admin\models\gii\crud\Generator',
                'templates' => [
                    'reactCrud' => '@app/vendor/webmens-ru/yii2-admin/src/models/gii/crud/react',
                ]
            ]
        ],
    ];
}

return $config;
