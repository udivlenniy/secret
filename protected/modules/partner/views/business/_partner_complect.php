<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 14.05.13
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */
?>
<div class = "partner-complekt">
    <div class="partner-complekt-photo"><?=CHtml::image($data->photopath,'Партнерский комплект',array('style'=>'width:80px;height:80px;'))?></div>
    <div class="partner-complekt-title"><?=$data->title?></div>
    <div class="partner-complekt-desc"><?=$data->getAttributeLabel('desc').' : '.$data->desc?></div>
    <div class="partner-complekt-price">
        <?=$data->getAttributeLabel('price').' : '.$data->price?>  руб.
        <?
            $model = Partner::model()->findByPk(Yii::app()->user->id);
            // если пользователь партнёр, то не вывод сумму с рег. взносом
            if($model->status==Partner::STATUS_MEMBER){
                echo '+ 400 руб. - регистрационный взнос';
            }
        ?>

        <div class="partner-complekt-buy-link">
            <?=$data->getAjaxLink('Купить', $this->createUrl('/partner/business/buyPartner'), array('id'=>$data->id))?>
        </div>

    </div>

<!--    <div class="partner-complekt-price">-->
    <?//=$data->price?>
    <!--</div>-->
</div>