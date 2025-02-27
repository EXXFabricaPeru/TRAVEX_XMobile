<?php

$params = array_merge(
        require __DIR__ . '/../../common/config/params.php',
        require __DIR__ . '/../../common/config/params-local.php',
        require __DIR__ . '/params.php',
        require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'name' => 'Xmobile - Middleware',
    'basePath' => dirname(__DIR__),
    'language' => 'es',
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    //'homeUrl' => '/xm/panel/',
    'modules' => [
        'lenguaje' => [
            'class' => 'backend\modules\admin\Localidades',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        //'baseUrl' => '/xm/panel',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend-test',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'excel'=>['class'=>'application.extensions.PHPExcel'],
    /*   'urlManager' => [
      'enablePrettyUrl' => true,
      'showScriptName' => false,
      'rules' => [
      ],
      ],

      'urlManager' => [
      'enablePrettyUrl' => true,
      'showScriptName' => false,
      'rules' => [
      ],
      ],
     */
    ],
	'modules' => [
        'gridview' => ['class' => 'kartik\grid\Module']
    ],
    'params' => $params,

    /*'import'=>[
            'application.models.*',
            'application.components.*',
            'application.vendors.phpexcel.classes.*',
    ],


    /*'components'=>[

        'excel'=>[ 'class'=>'PHPExcel-1.8\PHPExcel']

    ],*/
   // 'components'=>[

       //'excel'=>['class'=>'application.extensions.PHPExcel']

    //],
];
