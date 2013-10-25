<?php

class PostController extends CController {
	public function __construct($id, $module = NULL) {
		parent::__construct($id, $module);
		$this->defaultAction = 'list';
	}

	public function filters() {
		return array(
			'accessControl + update, delete',
			'postOnly + delete'
		);
	}

	public function accessRules() {
		return array(
			array(
				'allow',
				'actions' => array('list', 'view'),
				'users' => array('*')
			),
			array(
				'allow',
				'users' => array('@')
			),
			array(
				'deny',
				'users' => array('*')
			)
		);
	}

	public function actionList() {
		$criteria = new CDbCriteria(array('order' => 'create_time DESC'));
		if (isset($_GET['tag'])) {
			$criteria->addSearchCondition('tags', $_GET['tag']);
		}

		$data_provider = new CActiveDataProvider('Post', array(
			'criteria' => $criteria,
			'pagination' => array('pagesize' => Parameters::get()->
				posts_on_page)
		));

		$this->render('list', array('data_provider' => $data_provider));
	}

	public function actionControl() {
		$data_provider = new CActiveDataProvider('Post', array(
			'criteria' => array('order' => 'create_time DESC'),
			'pagination' => array('pagesize' => Parameters::get()->
				posts_on_page)
		));

		$this->render('control', array('data_provider' => $data_provider));
	}

	public function actionView($id) {
		$model = $this->loadModel($id);
		$this->render('view', array('model' => $model));
	}

	public function actionCreate() {
		$model = new Post;
		$this->performAjaxValidation($model);

		if (!isset($_POST['ajax']) and isset($_POST['Post'])) {
			$model->attributes = $_POST['Post'];
			$model->save();

			$this->redirect(array('list'));
		}

		if (!isset($_POST['ajax'])) {
			$this->render('create', array('model' => $model));
		}
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id);
		$this->performAjaxValidation($model);

		if (isset($_POST['Post'])) {
			$model->attributes = $_POST['Post'];
			$result = $model->save();
			if (!isset($_POST['ajax']) and $result) {
				$this->redirect(array('list'));
			}
		}

		if (!isset($_POST['ajax'])) {
			$this->render('update', array('model' => $model));
		}
	}

	public function actionDelete($id) {
		$this->loadModel($id)->delete();

		if (!isset($_POST['ajax'])) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] :
				array('list'));
		}
	}

	private function loadModel($id) {
		$model = Post::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}

	private function performAjaxValidation($model) {
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'post-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
