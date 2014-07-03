<?php

class ParametersForm extends CFormModel {
	public $password;
	public $password_copy;
	public $posts_on_page;
	public $maximal_width_of_images;
	public $dropbox_access_token;

	public function __construct($scenario = '') {
		parent::__construct($scenario);
		$this->posts_on_page = Parameters::get()->posts_on_page;
		$this->maximal_width_of_images =
			Parameters::get()->maximal_width_of_images;
		$this->dropbox_access_token = Parameters::get()->dropbox_access_token;
	}

	public function rules() {
		return array(
			array('password', 'safe'),
			array('password_copy', 'compare', 'compareAttribute' => 'password'),
			array('posts_on_page', 'numerical', 'min' => Parameters::
				MINIMUM_POSTS_ON_PAGE, 'max' => Parameters::
				MAXIMUM_POSTS_ON_PAGE),
			array(
				'maximal_width_of_images',
				'numerical',
				'min' => Parameters::MINIMUM_MAXIMAL_WIDTH_OF_IMAGES,
				'max' => Parameters::MAXIMUM_MAXIMAL_WIDTH_OF_IMAGES
			),
			array('dropbox_access_token', 'required'),
			array(
				'dropbox_access_token',
				'length',
				'max' => Parameters::DROPBOX_ACCESS_TOKEN_LENGTH_MAXIMUM,
				'tooLong' =>
					'{attribute} должен быть не длиннее {max} символов.'
			)
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль:',
			'password_copy' => 'Пароль (копия):',
			'posts_on_page' => 'Постов на страницу:',
			'maximal_width_of_images' => 'Максимальная ширина изображений:',
			'dropbox_access_token' => 'Токен доступа к Dropbox:'
		);
	}

	public function getParameters() {
		$attributes = array(
			'posts_on_page' => $this->posts_on_page,
			'maximal_width_of_images' => $this->maximal_width_of_images,
			'dropbox_access_token' => $this->dropbox_access_token
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
