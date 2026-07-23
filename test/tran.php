#!/usr/bin/env php
<?php





use wen202402\chain\chain\TronClient;

$sTime = microtime(true);

/**
 *
 */
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

$_libs=dirname(__DIR__).DIRECTORY_SEPARATOR;
require $_libs . 'vendor/autoload.php';
require $_libs . 'vendor/yiisoft/yii2/Yii.php';
require $_libs . '/common/config/bootstrap.php';
require $_libs . '/console/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require $_libs . 'common/config/main.php',
    require $_libs . 'common/config/main-local.php',
    require $_libs . '/console/config/main.php',
    require $_libs . '/console/config/main-local.php'
);
require $_libs.'vendor/autoload.php';

$application = new yii\console\Application($config);

$application->init();

$fromAddress = getenv('fromAddress');;
$privateKey  = getenv('privateKey');
$toAddress   = getenv('toAddress');;
$nliang   = getenv('nliang');

$usdtAmount = 1.5;
$tron=new TronClient();
$tron->init();
$tron->setCredentials($fromAddress,$privateKey);

var_dump($tron->getTrxBalance());

$tron->sendTrx($nliang, 0.8);
sleep(20);



var_dump($tron->getUsdtRawBalance($fromAddress));
var_dump($tron->transfer($toAddress, $usdtAmount));
echo 'ttt';

