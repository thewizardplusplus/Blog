<?php

class ParametersForm extends CFormModel {
	public $password;
	public $password_copy;
	public $posts_on_page;
	public $maximum_number_of_tags;

	public function __construct($scenario = '') {
		parent::__construct($scenario);
		$this->posts_on_page = Parameters::get()->posts_on_page;
		$this->maximum_number_of_tags = Parameters::get()->
			maximum_number_of_tags;
	}

	public function rules() {
		return array(
			array('password', 'safe'),
			array('password_copy', 'compare', 'compareAttribute' => 'password'),
			array('posts_on_page', 'numerical', 'min' => Parameters::
				MINIMUM_POSTS_ON_PAGE, 'max' => Parameters::
				MAXIMUM_POSTS_ON_PAGE),
			array('maximum_number_of_tags', 'numerical', 'min' => Parameters::
				MINIMUM_MAXIMUM_NUMBER_OF_TAGS, 'max' => Parameters::
				MAXIMUM_MAXIMUM_NUMBER_OF_TAGS)
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль:',
			'password_copy' => 'Пароль (копия):',
			'posts_on_page' => 'Пунктов на страницу:',
			'maximum_number_of_tags' => 'Максимальное число тегов в облаке:'
		);
	}

	public function getParameters() {
		$attributes = array(
			'posts_on_page' => $this->posts_on_page,
			'maximum_number_of_tags' => $this->maximum_number_of_tags
		);
		if (!empty($this->password)) {
			$attributes['password_hash'] = CPasswordHelper::hashPassword($this->
				password);
		}

		$parameters = Parameters::get();
		$parameters->attributes = $attributes;

		return $parameters;
	}
}
