<h3>Структура пользователя</h3>
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 08.05.13
 * Time: 14:55
 * To change this template use File | Settings | File Templates.
 */
$this->widget('CTreeView',array(
    'id'=>'unit-treeview',
    'url'=>array('tree'),
    //'data'=>$tree,
    'persist'=>'location', // метод запоминания открытого узла
    'unique'=>true, // если тру, то при открытии одного узла, будут закрываться остальные
    'htmlOptions'=>array(
        'class'=>'treeview-red'
    )
));