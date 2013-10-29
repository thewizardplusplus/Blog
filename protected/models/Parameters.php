<?php

class Parameters extends CActiveRecord {
	const RECORD_ID =                      1;
	const DEFAULT_PASSWORD_HASH =
		'$2a$13$7RC2CWHDqafP4dvl7t5PCucccPVl7spVT4FiALXEaxWCnzCTskqAK';
	const DEFAULT_POSTS_ON_PAGE =          10;
	const MINIMUM_POSTS_ON_PAGE =          1;
	const MAXIMUM_POSTS_ON_PAGE =          12;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public static function get() {
		$parameters = Parameters::model()->findByPk(Parameters::RECORD_ID);
		if (!is_null($parameters)) {
			return $parameters;
		} else {
			$parameters = new Parameters;
			$parameters->attributes = array(
				'password_hash' => Parameters::DEFAULT_PASSWORD_HASH,
				'points_on_page' => Parameters::DEFAULT_POINTS_ON_PAGE
			);
			$parameters->save();

			return $parameters;
		}
	}

	public function tableName() {
		return '{{parameters}}';
	}

	public function rules() {
		return array(
			array('id', 'default', 'value' => Parameters::RECORD_ID,
				'setOnEmpty' => FALSE),
			array('password_hash', 'required'),
			array('posts_on_page', 'numerical', 'min' => Parameters::
				MINIMUM_POSTS_ON_PAGE, 'max' => Parameters::
				MAXIMUM_POSTS_ON_PAGE)
		);
	}
}
