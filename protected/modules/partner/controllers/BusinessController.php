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
            'buyPartner'=>array(
                'class'=>'ext.actions.BuyPartnerAction',
                'modelName'=>'Partner',
            ),

            /*таблицы данных во вкладке - развитие бизнеса */
            'ajaxtbl'=>array(
                'class'=>'ext.actions.AjaxTblAction',
                'modelName'=>'Partner',
            ),
            // подробная информация о выбранном реферале в дереве
            'ajaxinfo'=>array(
                'class'=>'ext.actions.AjaxInfoAction',
                'modelName'=>'Partner',
                'view'=>'progress',
            )
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

        $dataProviderPartnershipSet = new CActiveDataProvider('PartnershipSet');

        $model = $this->loadPartner();


        $this->render('personal',array(
            'model'=>$model,
            'parent'=>$model->parent()->find(),
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

        $model = new Profit();

        $this->render('profit', array(
            'model'=>$model,
        ));
    }

    public function loadPartner($id=''){

        if(!empty($id)){
            $model = Partner::model()->findByPk($id);
        }else{
            $model = Partner::model()->findByPk(Yii::app()->user->id);
        }

        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /*
     * обрабатываем клик юзера по одному из разделов карточки клиента в дереве рефералов
     */
    /*
    public function actionAjaxInfo(){

        if(!Yii::app()->request->isAjaxRequest){ throw new CHttpException(400,'Invalid request'); }

        //echo '<pre>'; print_r($_REQUEST); die();

        // личные данные по выбранному юзеру из дерева
        if($_POST['type']=='personal'){
            //ID пользователя, ФИО пользователя/E-mail для новичков,Верифицированный телефонный номер
            $model = $this->loadPartner($_POST['id']);
            $this->renderPartial('_ajax_info_personal', array('model'=>$model));
            Yii::app()->end();
        }

        // бизнесс данные по пользователю
        if($_POST['type']=='business'){

            $partner = $this->loadPartner($_POST['id']);

            //$data[partner_level_from_me]-расстояние от текущего аккаунта до рассматриваемого сотрудника структуры

            // текущий партнёр
            $model = $this->loadPartner();

            $data['partner_level_from_me']= $partner->level - $model->level;

            $this->renderPartial('_ajax_info_business', array(
                'data'=>$data,
                'partner'=>$partner,
            ));
            Yii::app()->end();
        }
        // прибыль по партнёрской программе
        if($_POST['type']=='profit'){

            //$data['profit_for_me']-Моя прибыль по Партнерской Программе, сколько денег принём именно мне - выбранный юзер
            $profit = new Profit();

            $data['profit_for_me'] = $profit->inComeProfit('', '', (int)$_POST['id']);

            $this->renderPartial('_ajax_info_profit', array(
                'data'=>$data,
            ));
            Yii::app()->end();
        }

        throw new CHttpException(400,'The requested page does not exist.');
    }*/
}