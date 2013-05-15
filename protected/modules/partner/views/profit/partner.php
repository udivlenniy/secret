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

<strong>Заработано всего в Партнерской программе, баллов:</strong><?=$model->inComeProfit()?>
<br>

<strong>Заработано за отчетный период в Партнерской программе:</strong><?=$model->inComeProfit(strtotime($model->startMonth), strtotime($model->endMonth))?>
<br>

<strong>Ваш уровень в Партнерской Программе:</strong><?=$partner->partnerlevel?>
<br>

<strong>Кол-во рефералов на 1 уровне со статусом "Партнер":</strong><?=$partner->countChildren(Partner::STATUS_Partner, 1)?>
<br>

<strong>Кол-во приобретенных Вами Партнерских комплектов:</strong><?=$partner->partnershipCount?>
<br>

<strong>Кол-во баллов активности:</strong><?=$partner->active_points?>
<br>

<strong>Кол-во рефералов на 2-10 уровнях:</strong><?=$partner->countChildrenInIntervalLevels(2, 10)?>
<br>

<?
/*
Получатель бонуса
Размер бонуса, баллы
Отправитель бонуса
Кол-во пользователей в статусе Партнер у Вас на момент совершения транзакции
Кол-во личных Партнерских комплектов у Вас на момент совершения транзакции
Кол-во баллов активности у Вас на момент совершения транзакции
Ваш уровень в Партнерской программе на момент совершения транзакции
Кол-во баллов активности у отправителя на момент совершения транзакции
Уровень отправителя в Партнерской Программе на момент совершения транзакции
Уровень сотрудника как Вашего реферала
Дата и время совершения транзакции
Ваш бонус с 1 уровня рефералов на момент совершения транзакции, %
Ваш бонус с 2-10 уровней на момент совершения транзакции, %
*/
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$dataProvider,
    'id'=>'partner-grid',
    'template'=>'{items}{pager}',
    'filter'=>$model,
    'columns'=>array(
        array(
            'name'=>'destinationAccount',
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