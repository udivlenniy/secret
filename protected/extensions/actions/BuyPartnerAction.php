<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 15.05.13
 * Time: 9:31
 * To change this template use File | Settings | File Templates.
 */

class BuyPartnerAction extends CAction {
    /**
     * @var string name of the CActiveRecord class.
     */
    public $modelName;
    /**
     * @var string name of the search result view.
     */
    public $view;
    /**
     * Runs the action.
     */
    public function run()
    {
        if(!Yii::app()->request->isAjaxRequest){
            throw new CHttpException(400,'Invalid request');
        }

        //формируем массив с данными для отображения в форме для юзера
        $partner = $this->getModel()->findByPk(Yii::app()->user->id);

        // формируем цену партнёрского кмоплекта
        if($partner->status==Partner::STATUS_MEMBER){
            $price = 4000;//3600 - цена комплекта+ 400 рег. взнос
        }else{
            $price = 3600;
        }

        //проверяем достаточно ли средств для покупки
        if($partner->balance>=$price){
            // денег хватает - совершаем покупку комплекта
        }else{
            // не достаточно средств
            echo 'Ошибка! На вашем счете не хватает '.($price-$partner->balance).' баллов для покупки комплекта';
        }
    }
    /**
     * @return CActiveRecord
     */
    protected function getModel()
    {
        return CActiveRecord::model($this->modelName);
    }

    /*
     * покупка партнёрского аккаунта
     */
    public function buy($partner){

        //обрачиваем все операции в транзакцию
        $transaction = Yii::app()->db->beginTransaction();

        try {

            $transaction->commit();

        }catch (Exception $e){

            $transaction->rollback();

            throw $e;
        }
    }
}