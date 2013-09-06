<?php

/* @var $this PostController */
/* @var $model Post */

$this->pageTitle = Yii::app()->name . ' - Создать пост';

$this->renderPartial('_form', array('model' => $model));
