<?php

/* @var $this PostController */
/* @var $model Post */

$this->pageTitle = Yii::app()->name . ' - Изменить пост';

$this->renderPartial('_form', array('model' => $model));
