<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 13.05.13
 * Time: 17:04
 * To change this template use File | Settings | File Templates.
 */
?>
<h3>Доходы по партнерской программе</h3>

<strong>Сумма баллов, внесенная участниками Система для оплаты Партнерских комплектов:</strong><?=$model->inComeProfitAll()?>
<br>

<strong>Кол-в проданных Партнёрских комплектов</strong><?=BuyingPartnershipSet::numberSales()?>
<br>

<?
/*
ФИО
ID
Всего потрачено средств в рамках Партнерской Прогарммы
Приобретено Партнерских комплектов
Баллов активности
*/
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$dataProvider,
    'id'=>'partner-grid',
    'template'=>'{items}{pager}',
    'filter'=>$model,
    'columns'=>array(
        array(
            'name'=>'id',
            'header'=>'ФИО',
            'type'=>'raw',
            'value'=>'$data->destinationAccount->partner_soc_id',
            'filter'=>false,
        ),
        array(
            'name'=>'point',
            'value'=>'$data->point',
            'filter'=>false,
        ),
        array(
            'name'=>'senderAccount',
            'type'=>'raw',
            'value'=>'$data->senderAccount->partner_soc_id',
            'filter'=>false,
        ),
        array(
            'name'=>'has_partners',
            'type'=>'raw',
            'value'=>'$data->has_partners',
            'filter'=>false,
        ),
        array(
            'name'=>'has_personal_partners', 'type'=>'raw', 'value'=>'$data->has_personal_partners', 'filter'=>false,
        ),
        array(
            'name'=>'active_points', 'type'=>'raw', 'value'=>'$data->active_points', 'filter'=>false,
        ),
        array(
            'name'=>'partner_level', 'type'=>'raw', 'value'=>'$data->partner_level', 'filter'=>false,
        ),
        array(
            'name'=>'active_points_sender', 'type'=>'raw', 'value'=>'$data->active_points_sender', 'filter'=>false,
        ),
        array(
            'name'=>'partner_level_sender', 'type'=>'raw', 'value'=>'$data->partner_level_sender', 'filter'=>false,
        ),
        array(
            'name'=>'level_cooperator', 'type'=>'raw', 'value'=>'$data->level_cooperator', 'filter'=>false,
        ),
        array(
            'name'=>'create_at', 'type'=>'raw', 'value'=>'$data->create_at', 'filter'=>false,
        ),
        array(
            'name'=>'bonus_from_level1', 'type'=>'raw', 'value'=>'$data->bonus_from_level1', 'filter'=>false,
        ),
        array(
            'name'=>'bonus_from_other_levels', 'type'=>'raw', 'value'=>'$data->bonus_from_other_levels', 'filter'=>false,
        ),
        array(
            'class'=>'CButtonColumn',
            'visible'=>false,
        ),
    ),
));
?>