<?php

/* @var $this PostController */
/* @var $data_provider CActiveDataProvider */

$this->pageTitle = Yii::app()->name . ' - Посты';

$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider' => $data_provider,
	'template' => '{items} {pager}',
	'selectableRows' => 0,
	'columns' => array(
		array(
			'class' => 'CLinkColumn',
			'header' => 'Заголовок',
			'labelExpression' => '$data->title',
			'urlExpression' => '$this->grid->controller->createUrl(' .
				'"post/update", array("id" => $data->id))'
		),
		array(
			'type' => 'raw',
			'name' => 'Дата создания',
			'value' => 'Post::formatTime($data->create_time)'
		),
		array(
			'type' => 'raw',
			'name' => 'Дата изменения',
			'value' => 'Post::formatTime($data->modify_time)'
		),
		array(
			'class' => 'CButtonColumn',
			'header' => 'Действия',
			'buttons' => array(
				'view' => array('options' => array('title' =>
				'Посмотреть пост')),
				'update' => array('visible' => 'FALSE'),
				'delete' => array('options' => array('title' =>
				'Удалить пост'))
			)
		),
	),
	'pager' => array(
		'header' => '',
		'firstPageLabel' => '&lt;&lt;',
		'prevPageLabel' => '&lt;',
		'nextPageLabel' => '&gt;',
		'lastPageLabel' => '&gt;&gt;'
	)
));
