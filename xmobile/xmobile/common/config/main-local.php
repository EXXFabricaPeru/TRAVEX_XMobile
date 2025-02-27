<?php

$dir = \Yii::getAlias('@backend') . '/config/database.inc';
$file = fopen($dir, "r") or exit("Unable to open file!");
$resp = "";

while (!feof($file))
    $resp = fgets($file);
fclose($file);
$x = explode("#", $resp);
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost:3316;dbname=' . $x[0],
            'username' => $x[1],
            'password' => $x[2],
            'charset' => 'utf8',
        ],
        'hana' => [
            'class' => 'yii\db\Connection',
            'dsn' =>'odbc:driver={HDBODBC32};SERVERNODE='.$x[3].';DATABASE='.$x[4].';UID=SYSTEM;PWD='.$x[5].'#;charset=utf8mb4;CHAR_AS_UTF8=true',
            //'username' => 'SYSTEM',
            //'password' => '2021Exx3s',
            'charset' => 'utf8mb4',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
