<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'LoLRanks',
    'name' => 'LoL Ranks',
    //'language' => 'sr',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
		'GenericFunctions' => [
			'class' => 'app\components\GenericFunctions',
		],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) 
            // - this is required by cookie validation
            'cookieValidationKey' => 'REDACTED',
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			]
        ],
        // you can set your theme here 
        // - template comes with: 'default', 'slate', 'spacelab' and 'cerulean'
        'view' => [
            'theme' => [
                'pathMap' => ['@app/views' => '@webroot/themes/jhm/views'],
                'baseUrl' => '@web/themes/jhm',
            ],
        ],
        'assetManager' => [
            'bundles' => [
                // we will use bootstrap css from our theme
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [], // do not use yii default one
                ],
                // use bootstrap js from CDN
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'sourcePath' => null,   // do not use file from our server
                    'js' => [
                        'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js']
                ],
                // use jquery from CDN
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,   // do not use file from our server
                    'js' => [
                        '//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
                    ]
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
			'rules'=>[
				'groups/<slug:[A-Za-z0-9 -_.]+>'=>'group/view',
				'/'=>'site/index',
				'about'=>'site/about',
				'contact'=>'site/contact',
				'signup'=>'site/signup',
				'login'=>'site/login',
				'resetpassword'=>'site/request-password-reset',
				'account'=>'site/account',
			]
        ],
        'user' => [
            'identityClass' => 'app\models\UserIdentity',
            'enableAutoLogin' => false,
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'savePath' => '@app/runtime/session'
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => 'REDACTED',
				'username' => 'REDACTED',
				'password' => 'REDACTED',
				'port' => 'REDACTED',
				//'encryption' => 'tls',
			],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/translations',
                    'sourceLanguage' => 'en',
                ],
                'yii' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/translations',
                    'sourceLanguage' => 'en'
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
