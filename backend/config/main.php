<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\Admin',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        //优化网址
        'urlManager'=>[
            'enablePrettyUrl'=>true,
            'showScriptName'=>false,
            //'suffix'=>'.html' 网址后缀，可选项
        ],
        //语言包配置
        'i18n' =>
            [
                'translations' =>
                    [
                        '*' =>
                            [
                                // 引入语言包的配置类
                                'class' => 'yii\i18n\PhpMessageSource',
                                // 指向语言包的文件目录
                                //'basePath' => '/messages',  可选项
                                // 语言包的指向文件
                                'fileMap' =>
                                    [
                                        'common' => 'common.php',
                                    ],
                            ]
                    ]
            ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],

        'urlManager'=>
            [
                'showScriptName'=> false,
                'enablePrettyUrl'=> true,
                'rules'=>[],
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];
