<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 15.05.13
 * Time: 17:11
 * To change this template use File | Settings | File Templates.
 */

/*
 * класс для фиксирования покупок партнёрского комплекта пользователем системы
 */

Yii::import("application.components.functions", true);

class BuyPartnerComplekt {

    public $_partnerWhoBuy;// модель партнёра, кто покупает партнёрский комплект
    public $_partnerForWhom;// модель партнёра, для кого покупается партнёрский комплект
    public $_partner_ship_id;// ID партнёрского комплекта, который покупается

    public $who_buys;// кто покупает партнёрский комплект
    public $for_whom;// для кого была произвидена покупка партнёрского комплекта

    public $price = 3600;// цена партнёрского комплекта
    public $reg_payment = 0;// регистрационный взнос(платим лишь при первой покупке партнёрского комплекта)

    private $_connect;

    public  $error = false;
    public $error_text = '';

    private $for_me = true;

    // тип партнёр. комплекта
    private $type_partnership;

    public function run(){

        if(empty($this->who_buys)){
            $this->error = true;
            $this->error_text.='Не указана значение - кто покупает комплект';
        }

        if(empty($this->for_whom)){
            $this->error = true;
            $this->error_text.='Не указана значение - для кого покупается комплект';
        }

        if(!$this->error){
            if($this->who_buys!=$this->for_whom){
                $this->for_me = false;
            }
        }

        //если не указаны были ранее модели для работы с данными - определим модели
        if((empty($this->_partnerWhoBuy) || empty($this->_partnerForWhom)) && !$this->error){
            $this->_partnerWhoBuy = Partner::model()->findByPk($this->who_buys);
            if($this->who_buys==$this->for_whom){
                $this->_partnerForWhom = $this->_partnerWhoBuy;
            }else{
                $this->_partnerForWhom = Partner::model()->findByPk($this->for_whom);
            }
        }

        // не было ошибок, запускаем процесс покупки комплекта
        if(!$this->error){
            $this->buy();
        }
    }

    /*
     * основной метод покупки товара и начисления всех бонусов
     */
    public function buy(){

        //обрачиваем все операции в транзакцию
        $transaction = Yii::app()->db->beginTransaction();

        $this->_connect = Yii::app()->db;

        try {

            // если покупаем для себя комплект
            if($this->for_me){
                // цикл операций по начислению процентов и бонусов и проставления статусов и левелов, относительно покупки для себя
                $this->buyForMe();
            }

            // если покупаем партнёр. комплект для кого-то
            if($this->for_me){
                //пересчитываем данные, из расчёта, что комплект был куплен не для меня
                $this->buyForNotMe();
            }

            $transaction->commit();

        }catch (Exception $e){

            $transaction->rollback();

            //throw $e;
            $this->error = true;
            $this->error_text.= 'string:'.$e->getMessage().',<br> line:'.$e->getLine().', <br>Code:'.$e->getCode();
        }
    }

    /*
     * покупка партнёрского аккаунта для себя
     */
    public function buyForMe(){

        // массив значений, которые нужно обновить у записи
        $attributes = array();

        // НЕ первый партнёрский комплект
        if($this->_partnerWhoBuy->status==Partner::STATUS_Partner){

            // если он уже ПАРТНЁР, значит покупает себе НЕ_именной комплект(не первый)
            $this->type_partnership = BuyingPartnershipSet::TYPE_NONAME;

            // добавляем балы-активности за покупку НЕ_первого партнёрского комплета
            $this->_partnerWhoBuy->active_points = $this->_partnerWhoBuy->active_points+1;

            $attributes = array_merge(array('active_points'=>$this->_partnerWhoBuy->active_points+1),$attributes);

        }elseif($this->_partnerWhoBuy->status==Partner::STATUS_MEMBER){// ПЕРВЫЙ партнёрский комплект

            // если он участник, значит покупает себе именной комплект(первый)
            $this->type_partnership = BuyingPartnershipSet::TYPE_NAME;

            $this->reg_payment = 400;

            $this->_partnerWhoBuy->status = Partner::STATUS_Partner;

            $attributes = array_merge(array('status'=>Partner::STATUS_Partner),$attributes);
        }

        //списали с баланса юзера сумму покупки
        $attributes = array_merge(array('balance'=>$this->_partnerWhoBuy->balance-($this->price + $this->reg_payment)),$attributes);

        // есть данные для обновления - ОБНОВИМ
        if(!empty($attributes)){

            $this->_partnerWhoBuy->updateByPk($this->_partnerWhoBuy->id, $attributes);

            //====закфиксируем процесс покупки комплекта===============================
            $this->afterBuy();
        }

        // рекурсивный обход родителей текущего партнёра - купившего комплект
        $this->recursiveParents($this->_partnerWhoBuy);
    }

    /*
     * проходим вверх по списку родителей купившего партнёрский комплект аккаунта
     * и начисляем их бонусы за пополнение
     */
    public function recursiveParents($model){

        //рекурсивно получаем родителей купившего партнёрский комплект(уровни 1-10)
        for($i=1;$i<11;$i++){

            $attributes = array();

            $parent = $model->parent()->find();

            $sum_profit = 0;

            // если кончились родители, остановились
            if($parent===null){ break;  }

            //для уровня-1 -прибыль от приобретения комплекта - 20%(но только если он ПАРТНЁР)
            if($i==1){

                //добавим голосов(баллов активности)
                //$parent->active_points = $parent->active_points + 1;
                $attributes = array_merge(array('active_points'=>$parent->active_points + 1),$attributes);

                // прибыль получает ТОЛЬКО партнёр, НЕ участник
                if($parent->status==Partner::STATUS_Partner){

                    // сумма дохода по партнёру - его ПРОФИТ
                    $sum_profit = $parent->balance + percentFromValue($this->price, 20);

                    $attributes = array_merge(array('balance'=>$parent->balance + percentFromValue($this->price, 20)),$attributes);
                }
            }

            // подсчитываем -колво партнёров на нижестоящем уровне+кол-во не_именных комплектов(если он купил их)
            if($parent->status==Partner::STATUS_Partner){
                //кол-во партнёров в уровне-1
                $count_partner_level1 = $parent->countChildren(Partner::STATUS_Partner, 1);

                // кол-во неименных партнёрских комплектов
                $partnershipCount = $parent->partnershipCount;

                $parent->partner_level = $this->recalculatedPartnerLevel($count_partner_level1+$partnershipCount);

                $attributes = array_merge(array('partner_level'=>$parent->partner_level),$attributes);

                if($i!=1){
                    //$percent -  прибыль вышестоящего партнёра, после покупки нижестоящим партнёрского комплекта
                    $percent = 0;

                    // пересчитываем процент от прибыли партнёров уровня 2-10
                    $percent = $this->recalculatedPercentPartnerLevel($parent->partner_level);

                    //$parent->balance = $parent->balance + percentFromValue($this->price, $percent);

                    // сумма дохода по партнёру - его ПРОФИТ
                    $sum_profit = $parent->balance + percentFromValue($this->price, $percent);

                    $attributes = array_merge(array('balance'=>$parent->balance + percentFromValue($this->price, $percent)),$attributes);

                    $attributes = array_merge(array('bonus_from_other_levels'=>$percent),$attributes);
                }
            }

            // записываем операцию
            if(!empty($attributes)){

                $parent->updateByPk($parent->id, $attributes);

                //фиксируем операцию - дохода по партнёрке
                $this->fixProfit($parent, $sum_profit, $i);
            }

            // переназначение, для рекурсивного обхода следующих родителей вверх по иерархии
            $model = $parent;
        }
    }

    /*
     * пересчитаем процент для партнёра для уровня 2-10
     */
    public function recalculatedPercentPartnerLevel($partnerLevel){
        // серебряный уровень - 1%
        if($partnerLevel==Partner::SILVER_LEVEL){
            return 1;
        }
        // золотой уровень - 2%
        if($partnerLevel==Partner::GOLD_LEVEL){
            return 2;
        }
        // платиновый уровень - 3%
        if($partnerLevel==Partner::PLATINUM_LEVEL){
            return 3;
        }
        // юриллиантовый уровень - 4%
        if($partnerLevel==Partner::DIAMONT_LEVEL){
            return 4;
        }
        //нет уровня - 0
        if($partnerLevel==1){
            return 0;
        }
    }

    /*
     * перечистываем уровень партнёра в зависимости от кол-ва баллов активности
     */
    public function recalculatedPartnerLevel($count_active_points){

        if($count_active_points<2){
            return 0;
        }

        // 2 бала актив. - серебряный уровень партнёра
        if($count_active_points==2){
            return Partner::SILVER_LEVEL;
        }

        // 3 бала актив. - золотой уровень партнёра
        if($count_active_points==3){
            return Partner::GOLD_LEVEL;
        }
        // 4 бала актив. - платиновый уровень партнёра
        if($count_active_points==4){
            return Partner::PLATINUM_LEVEL;
        }
        // 5 бала актив. - бриллиантовый уровень партнёра
        if($count_active_points>=5){// если баллов активности более 5ти, то всё равно бриллиантовый-левел
            return Partner::DIAMONT_LEVEL;
        }
    }

    /*
     * покупка партнёрского аккаунта для каго-то из ниже стоящих рефералов
     */
    public function buyForNotMe(){

    }

    /*
     * записываем в лог, кто, для кого купил партнёрский комплект
     */
    public function afterBuy(){

        $buyPartnerShip = new BuyingPartnershipSet();
        // для кого покупается комплект
        $buyPartnerShip->partner_id = $this->_partnerForWhom->id;
        // какой комплект он покупает
        $buyPartnerShip->partnership_set_id = $this->_partner_ship_id;
        //тип комплекта
        $buyPartnerShip->type_buying = $this->type_partnership;

        $buyPartnerShip->save();
    }

    /*
     * фиксируем операции начисления прибылей
     * $partner - реферал верхнего уровня, по которому обновили данные
     * level_cooperator - уровень в иерархии
     */
    public function fixProfit($partner, $sum_profit, $level_cooperator){

        $profit = new Profit();

        //Получатель бонуса
        $profit->destination_account = $partner->id;

        // размер бонуса
        $profit->point = $sum_profit;

        // отправитель бонуса
        $profit->sender_account = $this->who_buys;

        //Кол-во пользователей в статусе Партнер у Вас после совершения транзакции
        $profit->has_partners =  $partner->countChildren(Partner::STATUS_Partner);

        //Кол-во личных Партнерских комплектов у Вас после совершения транзакции
        $profit->has_personal_partners = ($partner->partnershipCount+1);

        //Кол-во баллов активности у Вас после совершения транзакции
        $profit->active_points = $partner->active_points;

        //Ваш уровень в Партнерской программе после совершения транзакции
        $profit->partner_level = $partner->partner_level;

        //Кол-во баллов активности у отправителя после совершения транзакции
        $profit->active_points_sender = $this->_partnerWhoBuy->active_points;

        //Уровень отправителя в Партнерской Программе после совершения транзакции
        $profit->partner_level_sender = $this->_partnerWhoBuy->partner_level;

        //Уровень сотрудника как Вашего реферала
        $profit->level_cooperator = $level_cooperator;

        //Ваш бонус с 1 уровня рефералов после совершения транзакции, %
        $profit->bonus_from_level1 = 20;

        //Ваш бонус с 2-10 уровней после совершения транзакции, %
        $profit->bonus_from_other_levels = $partner->bonus_from_other_levels;

        $profit->save();
    }
}