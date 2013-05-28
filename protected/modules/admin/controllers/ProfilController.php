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
        $model->scenario = 'login';

        if(isset($_POST['Login']) && Yii::app()->request->isAjaxRequest)
        {

            if(isset($_POST['Login']['smsCode'])){
                $model->scenario = 'sms';
            }else{
                $model->scenario = 'login';
            }

            $model->attributes=$_POST['Login'];
            // validate user input and redirect to the previous page if valid
            // прошли валидацию по логину и паролю, теперь показываем окно для ввода смс-кода
            if($model->validate()){

                if($model->scenario=='login'){

                    //отправляем уведомление на почту админа об попытке авторизации
                    $message = new YiiMailMessage('Авторизация в админке системы');
                    //$message->view = 'registrationFollowup';
                    //userModel is passed to the view
                    $message->setBody('Кто-то пытался получить доступ к админскому аккаунту "Системы" в '.date('d-m-Y H:i:s').', с IP адреса '.Yii::app()->request->getUserHostAddress(), 'text/html');
                    $message->addTo(Yii::app()->params['adminEmail']);
                    $message->from = Yii::app()->params['adminEmail'];
                    Yii::app()->mail->send($message);

                    //отправляем СМС-сообщение с кодом, для успешной авторизации
                    $user = new Partner();
                    $user->phoneSms = Yii::app()->config->get('ADMIN.PHONE1');
                    $user->rndSmsCode();

                    $user->textSms = 'Код:'.$user->codeSms;
                    $user->checkParams();
                    $user->sendSms();


                    //echo 'Код:'.$user->codeSms;
                    //- true - сообщение доставлено, false -  не досталено(в error_descSms - описание ошибки)
                    if(!$user->isDeliveredSms()){
                        $model->addError('smsCode', 'При отправке смс, возникли проблемы-'.$user->error_descSms);
                    }
                    $this->renderPartial('login', array(
                        'model'=>$model,
                        'showFileds' => true,
                    ));
                }else{

                    $showFileds = false;

                    // авторизовываем юзера системы, записываем кукисы и сессию
                    if($model->validate() && $model->login()){
                        echo 'ok';
                    }
                }
            }else{
                if($model->scenario=='login'){
                    $showFileds = false;
                }else{
                    $showFileds = true;
                }
                $this->renderPartial('login', array(
                    'model'=>$model,
                    'showFileds' => $showFileds,
                ));
            }

            Yii::app()->end();
        }

        $this->render('login', array(
           'model'=>$model,
            'showFileds' => false,
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

        $model = new AdminProfil();

        if(isset($_POST['AdminProfil'])){

            $model->attributes = $_POST['AdminProfil'];

            if($model->validate()){

                Yii::app()->config->set('ADMIN.EMAIL',$model->email);

                Yii::app()->config->set('ADMIN.PHONE1',$model->phone1);

                Yii::app()->config->set('ADMIN.PHONE2',$model->phone2);

                // если указали пароль(новый)
                if(!empty($model->password_new)){
                    $partner = new Partner();
                    Yii::app()->config->set('ADMIN.PASSWORD', $partner->encrypting($model->password_new));
                }

                Yii::app()->user->setFlash('update','Данные профиля успешно обновлены');

                $this->refresh();
            }
        }else{
            $model->email = $model->getEmail();
            $model->phone1 = $model->getPhone1();
            $model->phone2 = $model->getPhone2();
        }

        $this->render('update', array(
            'model'=>$model,
        ));
    }

}