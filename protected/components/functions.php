<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 16.05.13
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */

/*
 * подсчитываем процент от числа
 * вычитаем или прибавляем к числу некий процент
 */
function percentFromValue($value, $percent){

    $value_percent = ($percent*$value)/100;

    $result = xaround($value_percent);

    return $result;
}

/*
 * функция округления числа до нужного значения
 */
function xaround($value){

    // округлили до сотых
    $value = round($value,2);

    //округление в меньшую сторону
    return floor($value);
}