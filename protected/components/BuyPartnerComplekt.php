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
class BuyPartnerComplekt {

    public $_partnerModel;// модель партнёра
    public $_partnerComplektModel;// модель Партнёрского комплекта

    public $who_buy;// кто покупает партнёрский комплект
    public $for_whom;// для кого была произвидена покупка партнёрского комплекта


    public $price;// цена партнёрского комплекта

    public $_connect;

    /*
     * основной метод покупки товара и начисления всех бонусов
     */
    public function buy(){

        //обрачиваем все операции в транзакцию
        $transaction = Yii::app()->db->beginTransaction();

        $this->_connect = Yii::app()->db;

        try {

            $transaction->commit();

        }catch (Exception $e){

            $transaction->rollback();

            throw $e;
        }
    }

    /*
     * обновляем данные относительно того, кто купил данный комплект
     * списываем деньги с баланса,
     */
    public function updatePartnerProfil(){
        // списывааем с баланса того, кто купил партнёрский комплект - стоимомсть партнёрского комплекта
        //запрос на обновление параметров по юзеру, котор. купил комплект
        $sql = 'UPDATE {{partner}} SET balance=:balanceMinus, partner_level=:partner_level,active_points=:active_points,status=:status WHERE id=:id';
        $query = $this->_connect->createCommand($sql);
        $query->bindValue(':balanceMinus', ($this->_partnerModel->balance-$this->price), PDO::PARAM_INT);
        $query->bindValue(':active_points',($this->_partnerModel->active_points+1), PDO::PARAM_INT);
        $query->bindValue(':status', Partner::STATUS_Partner, PDO::PARAM_INT);
        $query->bindValue(':id', $this->_partnerModel->id, PDO::PARAM_INT);
        $query->bindValue();
        $query->bindValue();

        $query->execute();
    }

    /*
     * скидка по партнёрской программе в зависимости от уровня пользователя
     */
    public function getDiscountFromPartnerLevel($new_level_partner){

    }

    /*
     * получаем НОВЫЙ уровень партнёра на основании данной покупки
     * $countChildrenInLevel1 - кол-во рефералов уровня(1), по юзеру котор. покупает комплект+не_именные партнёрские комплекты(включая купленный в данный момент)
     */
    public function getPartnerLevel($countChildrenInLevel1){
        if($countChildrenInLevel1==Partner::SILVER_LEVEL){
            //return
        }
    }
}