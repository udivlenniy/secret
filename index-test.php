<?php
/**
 * This is the bootstrap file for test application.
 * This file should be removed when the application is deployed for production.
 */

// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/protected/config/test.php';

// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once($yii);
Yii::createWebApplication($config)->run();


// for htmlOptions
'onclick' => '{' . CHtml::ajax(array(
    'url'=>$url,
    'type'=>'POST',
    'data'=>$data,
    'beforeSend' => 'js:function(eventObject){
                        }',
    'success' => "js:function(html){
                            //$('#".$id."').attr('title',html);
                            //$('#".$id."').delay(800);
                            //active_tiptip();
                            //$('#".$id."').tipTip({defaultPosition: 'right', 'activation':'hover', delay:0, maxWidth:'auto', 'content':html});
                        }")) .
'return false;
}', // returning false prevents the default navigation to another url on a new page



' . CHtml::ajax(array(
                        'url'=>$url,
                        'type'=>'POST',
                        'data'=>$data,
                        'beforeSend' => 'js:function(eventObject){
}',
                        'success' => "js:function(html){
                            //$('#".$id."').attr('title',html);
                            //$('#".$id."').delay(800);
                            //active_tiptip();
                            //$('#".$id."').tipTip({defaultPosition: 'right', 'activation':'hover', delay:0, maxWidth:'auto', 'content':html});
                        }")) .
                    '