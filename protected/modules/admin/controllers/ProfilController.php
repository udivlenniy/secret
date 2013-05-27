<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 24.05.13
 * Time: 14:04
 * To change this template use File | Settings | File Templates.
 */

/*
 * контроллер для авторизации, смены пароля у админа и разлогинивания
 */
class ProfilController extends BaseAdminController {

    public $defaultAction = 'login';

    /*
     * авторизация админа
     */
    public function actionLogin(){

        if(!Yii::app()->user->isGuest && Yii::app()->user->role==Partner::ROLE_ADMIN){
            $this->redirect('profil');
        }

        $model = new Login();

        if(isset($_POST['Login']['smsCode'])){
            $model->scenario = 'sms';
        }else{
            $model->scenario = 'login';
        }

//        if(isset($_POST['ajax']) && $_POST['ajax']==='admin-form')
//        {
//            echo CActiveForm::validate($model);
//            Yii::app()->end();
//        }

        // collect user input data
        if(isset($_POST['Login']) && Yii::app()->request->isAjaxRequest)
        {
            $model->attributes=$_POST['Login'];
            // validate user input and redirect to the previous page if valid
            // прошли валидацию по логину и паролю, теперь показываем окно для ввода смс-кода
            if($model->validate()){

                if($model->scenario=='login'){
                    $this->renderPartial('login', array(
                        'model'=>$model,
                        'showFileds' => false,
                    ));

                }else{
                    $showFileds = false;
                }



            }else{

                if($model->scenario=='login'){
                    $showFileds = true;
                }else{
                    $showFileds = false;
                }

                $this->renderPartial('login', array(
                    'model'=>$model,
                    'showFileds' => $showFileds,
                ));
            }

            Yii::app()->end();
        }
        //&& $model->login()

        $this->render('login', array(
           'model'=>$model,
            'showFileds' => true,
        ));
    }

    /*
     * разлогинирование админа
     */
    public function actionLogOut(){
        Yii::app()->user->logout();
        $this->redirect('login');
    }

    /*
     * редактирование данных для авторизации админа
     */
    public function actionUpdate(){

    }

}