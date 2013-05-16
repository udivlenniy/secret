<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 08.05.13
 * Time: 14:19
 * To change this template use File | Settings | File Templates.
 */
?>
<h2>Личное развитие:</h2>

<strong>Статус:</strong> <?=$model->statuspartner;?> <br>

<strong>Уровень в партнерской программе:</strong> <?=$model->partnerlevel?> <br>

<strong>Процент отчислений с взносов сотрудников 2-10 уровней:</strong> <?=$model->bonus_from_other_levels?>% <br>

<!--доступно только Участникам и Партнерам-->
<!--<strong>Уровень в потребительской программе:</strong> --><?//=$status?><!-- <br>-->

<?
if($parent!==null){
    echo '<strong>Id спонсора:</strong> '.$parent->id.'<br>';
}
?>

<?
if($parent!==null){
    echo '<strong>ФИО спонсора:</strong> '.$parent->fio.'<br><br><br>';
}
?>

<?
//_partner_complects

$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$dataProviderPartnerComplekts,
    'itemView'=>'_partner_complect',   // refers to the partial view named '_post'
    'viewData'=>array(
        'status'=>$model->status
    ),
    'template'=>'{items}',
    'emptyText'=>'',
    /*'sortableAttributes'=>array(
        'title',
        'create_time'=>'Post Time',
    ),*/
));
?>