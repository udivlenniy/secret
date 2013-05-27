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

/*

        $result_send =  $this->send("api.smsfeedback.ru", 80, "amazing1", "zx78op01","+380978800696", "text here", "TEST-SMS");

        echo $result_send.'<br>'; flush();

        //анализируем ответ от запроса на отправку смс
        $expl = explode(';', $result_send);

        echo 'now='.date('d-m-Y H:i:s', time()).'<br>';flush();

        for($i=0;$i<200;$i++){

            $url = 'http://amazing1:zx78op01@api.smsfeedback.ru/messages/v2/status/?id='.$expl[1];

            $result = file_get_contents($url);

            $exp_result = explode(';', $result);

            // сообщение доставлено абоненту
            if($exp_result[1]=='delivered'){
                die('end-'.date('d-m-Y H:i:s', time()));
            }

            echo date('d-m-Y H:i:s', time()).'|'.$url.'<br>';flush();

            sleep(1);
        }

        die();*/
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

                    /*
                    //отправляем уведомление на почту админа об попытке авторизации
                    $message = new YiiMailMessage('Авторизация в админке системы');
                    //$message->view = 'registrationFollowup';
                    //userModel is passed to the view
                    $message->setBody('Кто-то пытался получить доступ к админскому аккаунту "Системы" в '.date('d-m-Y H:i:s').', с IP адреса '.Yii::app()->request->getUserHostAddress(), 'text/html');
                    $message->addTo(Yii::app()->params['adminEmail']);
                    $message->from = Yii::app()->params['adminEmail'];
                    Yii::app()->mail->send($message);*/

                    //отправляем СМС-сообщение с кодом, для успешной авторизации
                    $user = new Partner();
                    $user->phoneSms = Yii::app()->config->get('ADMIN.PHONE1');
                    $user->rndSmsCode();
                    /*
                    $user->textSms = 'Код:'.$user->codeSms;
                    $user->checkParams();
                    $user->sendSms();
                    */

                    echo 'Код:'.$user->codeSms;
                    //- true - сообщение доставлено, false -  не досталено(в error_descSms - описание ошибки)
                    //if(!$user->isDeliveredSms()){
                        //$model->addError('smsCode', 'При отправке смс, возникли проблемы-'.$user->error_descSms);
                    //}
                    $this->renderPartial('login', array(
                        'model'=>$model,
                        'showFileds' => true,
                    ));
                }else{

                    $showFileds = false;

                    $model->username = $_POST['Login']['username'];
                    $model->password = $_POST['Login']['password'];

                    //echo 'scenario='.$model->scenario.'<br>';

                    if(!$model->validate()){
                        //echo 'errors='; print_r($model->errors);
                    }

                    if(Yii::app()->user->isGuest){ /*echo 'guest<br>';*/}

                    // авторизовываем юзера системы, записываем кукисы и сессию
                    if($model->validate() && $model->login()){
                        echo 'ok';
                    }else{
                        print_r($model->errors);
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

    }

}