<?php

class ParametersController extends CController {
	public function __construct($id, $module = NULL) {
		parent::__construct($id, $module);
		$this->defaultAction = 'update';
	}

	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(
			array(
				'allow',
				'actions' => array('update'),
				'users' => array('admin')
			),
			array(
				'deny',
				'users' => array('*')
			)
		);
	}

	public function actionUpdate() {
		$model = new ParametersForm;

		if (isset($_POST['ajax']) && $_POST['ajax'] === 'parameters-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['ParametersForm'])) {
			$model->attributes = $_POST['ParametersForm'];
			$result = $model->validate();
			if ($result) {
				$model->save();

				$model->password = '';
				$model->password_copy = '';
			}
		}

		if (!isset($_POST['ajax'])) {
			$this->render('update', array('model' => $model));
		}
	}
}
