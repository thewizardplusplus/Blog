<?php

/* @var $this PostController */
/* @var $model Post */

$this->pageTitle = Yii::app()->name . ' - ' . $model->title;

$this->renderPartial('_view', array('data' => $model));
