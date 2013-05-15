<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 07.05.13
 * Time: 18:27
 * To change this template use File | Settings | File Templates.
 */
class BusinessController extends BaseUserController{

    /**
     * @var string name of the model class.
     */
    public $modelName = 'Partner';

    public $parent_id = 'parent_id';
    public $label = 'fio';

    public function actions(){
        return array(
            /* ajax запрос для построения дерева рефералов текущего юзера
             * при первом запуске $showRoot=true, потому что нужно отобразить родителя(текущего юзера во главе дерева)
             * при последующих запросах родителя не отображаем*/
            'tree'=>array(
                'class'=>'ext.actions.TreeAction',
                'modelName'=>'Partner',
            ),
            /*
             * развитие бизнесса/ данные об общих количествах сотрудников разных уровней и статусов
             * + ссылки на детализацию данных
             */
            'progress'=>array(
                'class'=>'ext.actions.ProgressAction',
                'modelName'=>'Partner',
                'view'=>'progress',
            ),
            // подробная информация о выбранном типе рефералов, указанного уровня или статуса
            /*'ajax_progress'=>array(
                'class'=>'ext.actions.AjaxProgressAction',
                'modelName'=>'Partner',
                'view'=>'progress',
            )*/
        );
    }

    public function actionIndex()
    {
        // отображаем вкладки с данные по пользователю
        $this->render('index');
    }

    public function actionStructure(){
        $this->render('structure');
    }

    /*
     * личное развитие бизнесса
     */
    public function actionPersonal(){

        if(!Yii::app()->request->isAjaxRequest){
            //throw new CHttpException(400,'Invalid request');
        }

        //формируем массив с данными для отображения в форме для юзера
        //$data = $this->loadPartner();

        $dataProviderPartnershipSet = new CActiveDataProvider('PartnershipSet');

        $this->render('personal',array(
            'model'=>$this->loadPartner(),
            'dataProviderPartnerComplekts'=>$dataProviderPartnershipSet
        ));
    }

    /*
     * доход пользователя от партнёрской программы+потребительской программы
     */
    public function actionProfit(){
        if(!Yii::app()->request->isAjaxRequest){
            //throw new CHttpException(400,'Invalid request');
        }
        $this->render('profit');
    }

    /**
     * подробная информация о выбранном типе рефералов, указанного уровня или статуса
     */
    public function actionAjaxTbl(){

        if(!Yii::app()->request->isAjaxRequest){ throw new CHttpException(400,'Invalid request'); }

        // относительно текущего юзера производим выборку его дочерних рефералов по указанному типу(статусу)
        $model = $this->loadPartner();
        $model->setScenario('search');
        //$model->unsetAttributes();
        if(isset($_GET['Partner'])){
            $model->attributes = $_GET['Partner'];
        }

        $criteria=new CDbCriteria;
        $criteria->condition = 'lft>'.$model->lft.' AND rgt<='.$model->rgt.' ';

        if($model->type==Partner::STATUS_MEMBER){
            $criteria->addColumnCondition(array('status'=>Partner::STATUS_MEMBER));
        }
        if($model->type==Partner::STATUS_Partner){
            $criteria->addColumnCondition(array('status'=>Partner::STATUS_Partner));
        }
        if($model->type=='partner_level1'){
            $criteria->condition = 'status='.Partner::STATUS_Partner.' AND level<='.($model->level+1).'';
        }

        $dataProvider = new CActiveDataProvider('Partner', array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>100,
            ),
        ));

        $this->renderPartial('ajax', array(
            'dataProvider'=>$dataProvider,
            'model'=>$model,
            )
        );
    }

    public function loadPartner(){

        $model = Partner::model()->findByPk(Yii::app()->user->id);

        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }
}