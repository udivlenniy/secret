<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 13.05.13
 * Time: 17:03
 * To change this template use File | Settings | File Templates.
 */

class ProfitController extends BaseUserController {

    public function actions(){
        return
            array(
                'buyPartner'=>array(
                    'class'=>'ext.actions.BuyPartnerAction',
                    'modelName'=>'Partner',
                ),
            );
    }

    /*
     *доходы по партнерской программе
     */
    public function actionPartner(){

        $model = new Profit();
        $model->setScenario('search');
        if(isset($_GET['Profit'])){
            $model->attributes = $_GET['Profit'];
        }

        $criteria = new CDbCriteria;

        if($model->dateFrom){
            $criteria->condition = 'create_at>'.strtotime($model->dateFrom);
        }

        if($model->dateTo){
            $criteria->condition = 'create_at<'.strtotime($model->dateTo);
        }

        $dataProvider = new CActiveDataProvider('Profit', array(
            'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=>'t.id DESC',
            ),
            'pagination'=>array(
                'pageSize'=>100,
            ),
        ));

        $this->render('partner', array(
            'dataProvider'=>$dataProvider,
            'model'=>$model,
            'partner'=>$this->loadPartner(),
        ));
    }


    /*
     * информация о партнёре, по текущего юзеру
     */
    public function loadPartner(){

        $model = Partner::model()->findByPk(Yii::app()->user->id);

        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }
}