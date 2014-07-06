<?php

class LoginForm extends CFormModel {
	public $password;

	public function rules() {
		return array(
			array('password', 'required'),
			array('password', 'authenticate')
		);
	}

	public function attributeLabels() {
		return array('password' => 'Пароль:');
	}

	public function authenticate($attribute, $params) {
		if (!$this->hasErrors()) {
			$this->identity = new UserIdentity($this->password);
			if (!$this->identity->authenticate()) {
				$this->addError('password', 'Неверный пароль.');
			}
		}
	}

	public function login() {
		if(is_null($this->identity)) {
			$this->identity = new UserIdentity($this->password);
			$this->identity->authenticate();
		}

		if ($this->identity->errorCode === UserIdentity::ERROR_NONE) {
			Yii::app()->user->login($this->identity);

			return true;
		} else {
			return false;
		}
	}

	private $identity;
}
