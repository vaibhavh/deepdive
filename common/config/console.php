<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/main-local.php');
$rildb = require(__DIR__ . '/rildb.php');
$mongodb = require(__DIR__ . '/mongodb.php');
//$amqp = require(__DIR__ . '/amqp.php');
$aliases = require(__DIR__ . '/custom_aliases.php');
$modules = require(__DIR__ . '/modules.php');
//require (dirname(__DIR__) . '/vendor/Net_Nmap/Net/Nmap.php');


$modules['gii'] = 'yii\gii\Module';
$modules['encrypter'] = 'nickcv\encrypter\Module';

return [
    'id' => 'anant-networks-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii', 'encrypter'],
    'controllerNamespace' => 'app\commands',
    'modules' => $modules,
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'rildb' => $rildb,
        'mongodb' => $mongodb,
        //'amqp' => $amqp,
        'encrypter' => require(__DIR__ . DIRECTORY_SEPARATOR . 'encrypter.php'),
//        'net' => [
//            'class' => 'app\components\network\NetworkAdaptor',
//        ],
    ],
    'params' => $params,
//    'controllerMap' => [
//        'mongodb-migrate' => 'yii\mongodb\console\controllers\MigrateController',
//    ],
];
