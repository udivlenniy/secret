<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
    private $_id;
    private $_status;

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
        // поиск пользователя по почте в БД
        $model = Partner::model()->findByAttributes(array('email'=>$this->username));

		if($model===null){

            $this->errorCode=self::ERROR_USERNAME_INVALID;

        }elseif($model->password!==$model->encrypting($this->password)){

            $this->errorCode=self::ERROR_PASSWORD_INVALID;

        }else{

            $this->errorCode=self::ERROR_NONE;

            $this->_id = $model->id;
            $this->_status = $model->status;

            $this->setState('status', $model->status);
            $this->setState('role', $model->role);
            $this->setState('id', $model->id);
        }

		return !$this->errorCode;
	}

    /**
     * @return integer the ID of the user record
     */
    public function getId()
    {
        return $this->_id;
    }

    public function getStatus(){
        return $this->_status;
    }
}