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
//$partner = new Partner();
//echo 'pass='.$partner->encrypting('1');
//Yii::app()->user->id = 2;
//Yii::app()->user->logout();

