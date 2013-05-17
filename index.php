<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);

Yii::createWebApplication($config)->run();


$root = Partner::model()->findByPk(11);
//$root->encrypting('1');
/*
for($i=2;$i<22;$i++){
    $root = Partner::model()->findByPk($i)->deleteNode();
}*/
/*
for($i=0;$i<4;$i++){
    $partner = new Partner();
    $partner->fio = 'Пупкин-'.$i;
    $partner->email = 'pypkin'.$i.'@mail.ru';
    $partner->balance = 4000;
    $partner->password = $partner->encrypting('1');
    $partner->prependTo($root);
}*/