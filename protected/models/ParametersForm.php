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
			array(
				'posts_on_page',
				'numerical',
				'min' => Constants::MINIMUM_POSTS_ON_PAGE,
				'max' => Constants::MAXIMUM_POSTS_ON_PAGE
			),
			array(
				'maximal_width_of_images',
				'numerical',
				'min' => Constants::MINIMUM_MAXIMAL_WIDTH_OF_IMAGES,
				'max' => Constants::MAXIMUM_MAXIMAL_WIDTH_OF_IMAGES
			),
			array('dropbox_access_token', 'required'),
			array(
				'dropbox_access_token',
				'length',
				'max' => Constants::DROPBOX_ACCESS_TOKEN_LENGTH_MAXIMUM,
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

	public function save() {
		$parameters = Parameters::get();
		if (!empty($this->password)) {
			$parameters->password_hash = CPasswordHelper::hashPassword(
				$this->password
			);
		}
		$parameters->posts_on_page = $this->posts_on_page;
		$parameters->maximal_width_of_images = $this->maximal_width_of_images;
		$parameters->dropbox_access_token = $this->dropbox_access_token;
		$parameters->save();
	}
}
