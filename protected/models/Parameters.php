<?php

class Parameters extends CActiveRecord {
	const RECORD_ID = 1;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public static function get() {
		$parameters = Parameters::model()->findByPk(Parameters::RECORD_ID);
		if (is_null($parameters)) {
			$parameters = new Parameters;
			$parameters->password_hash = CPasswordHelper::hashPassword(
				Constants::DEFAULT_PASSWORD
			);
			$parameters->save();
		}

		return $parameters;
	}

	public function tableName() {
		return '{{parameters}}';
	}
}
