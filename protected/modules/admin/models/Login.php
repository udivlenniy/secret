<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 24.05.13
 * Time: 11:57
 * To change this template use File | Settings | File Templates.
 */

/*
 * хитрожопая авторизация админа
 */
class Login extends CFormModel{

    public $username;
    public $password;
    public $smsCode;

    private $_identity;

    public function rules()
    {
        return array(
            // username and password are required
            array('username, password', 'required','on'=>'login'),
            array('username','email','on'=>'login'),
            // rememberMe needs to be a boolean
            //array('rememberMe', 'boolean'),
            // password needs to be authenticated
            array('password', 'authenticate','on'=>'login'),

            // проверяем отправленный смс-код на телефон
            array('smsCode', 'required','on'=>'sms'),
            array('smsCode','validateSms', 'on'=>'sms'),
        );
    }

    public function validateSms(){
        $model = new Partner();
        if($model->isValidateSmsCode($this->smsCode)){
            return true;
        }else{
            $this->addError('smsCode', 'Код смс указан не верно');
            return false;
        }
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'rememberMe'=>'Remember me next time',
            'username'=>'Почта',
            'password'=>'Пароль',
            'smsCode'=>'код смс',
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute,$params)
    {
        if(!$this->hasErrors())
        {

            $this->_identity=new AUserIdentity($this->username,$this->password);

            if(!$this->_identity->authenticate()){
                $this->addError('password','Не правильно указан логин или пароль.');
            }
        }
    }

    /**
     * Logs in the user using the given username and password in the model.
     * @return boolean whether login is successful
     */
    public function login()
    {
        if($this->_identity===null)
        {
            $this->_identity=new AUserIdentity($this->username,$this->password);
            $this->_identity->authenticate();
        }
        if($this->_identity->errorCode===AUserIdentity::ERROR_NONE)
        {
            //$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
            $duration = 0;
            Yii::app()->user->login($this->_identity,$duration);
            return true;
        }
        else
            return false;
    }
}