<?php

class ParametersForm extends CFormModel {
	public $password;
	public $password_copy;
	public $posts_on_page;
	public $maximal_width_of_images;
	public $versions_of_backups;

	public function __construct($scenario = '') {
		parent::__construct($scenario);
		$this->posts_on_page = Parameters::get()->posts_on_page;
		$this->maximal_width_of_images =
			Parameters::get()->maximal_width_of_images;
		$this->versions_of_backups = Parameters::get()->versions_of_backups;
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
			array('versions_of_backups', 'numerical', 'min' => Parameters::
				MINIMUM_VERSIONS_OF_BACKUPS, 'max' => Parameters::
				MAXIMUM_VERSIONS_OF_BACKUPS)
		);
	}

	public function attributeLabels() {
		return array(
			'password' => 'Пароль:',
			'password_copy' => 'Пароль (копия):',
			'posts_on_page' => 'Постов на страницу:',
			'maximal_width_of_images' => 'Максимальная ширина изображений:',
			'versions_of_backups' => 'Версий бекапов:'
		);
	}

	public function getParameters() {
		$attributes = array(
			'posts_on_page' => $this->posts_on_page,
			'maximal_width_of_images' => $this->maximal_width_of_images,
			'versions_of_backups' => $this->versions_of_backups
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
