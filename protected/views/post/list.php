<?php

/* @var $this PostController */

$this->pageTitle = Yii::app()->name;

if (Yii::app()->user->isGuest) {
	if (!empty($_GET['tag'])) {
		echo '<p>Посты с тегом &laquo;' . CHtml::encode($_GET['tag']) .
			'&raquo;:</p>';
	}

	$this->widget('zii.widgets.CListView', array(
		'dataProvider' => $dataProvider,
		'template' => '{items} {pager}',
		'itemView' => '_view',
		'pager' => array(
			'maxButtonCount' => PostController::MAXIMUM_PAGINATION_BUTTON_COUNT,
			'header' => '',
			'firstPageLabel' => '&lt;&lt;',
			'prevPageLabel' => '&lt;',
			'nextPageLabel' => '&gt;',
			'lastPageLabel' => '&gt;&gt;'
		)
	));
} else {
	$dummy_post = new Post;
	$title_column_header = $dummy_post->getAttributeLabel('title');
	$this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider' => $dataProvider,
		'template' => '{items} {pager}',
		'selectableRows' => 0,
		'enableSorting' => FALSE,
		'columns' => array(
			array(
				'class' => 'CLinkColumn',
				'header' => $title_column_header,
				'labelExpression' => '$data->title',
				'urlExpression' => '$this->grid->controller->createUrl(' .
					'"post/update", array("id" => $data->id))'
			),
			array(
				'name' => 'text',
				'value' => 'Post::processText("list:admin", $data->text)'
			),
			array(
				'name' => 'create_time',
				'value' => 'Post::formatTime($data->create_time)'
			),
			array(
				'name' => 'modify_time',
				'value' => 'Post::formatTime($data->modify_time)'
			),
			array(
				'name' => 'tags',
				'type' => 'html',
				'value' => '!empty($data->tags) ? $data->tags : "&mdash;"'
			),
			array(
				'class' => 'CButtonColumn',
				'buttons' => array(
					'view' => array('visible' => 'FALSE'),
					'update' => array('visible' => 'FALSE')
				)
			),
		),
		'pager' => array(
			'maxButtonCount' => PostController::MAXIMUM_PAGINATION_BUTTON_COUNT,
			'header' => '',
			'firstPageLabel' => '&lt;&lt;',
			'prevPageLabel' => '&lt;',
			'nextPageLabel' => '&gt;',
			'lastPageLabel' => '&gt;&gt;'
		)
	));
}
