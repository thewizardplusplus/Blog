<?php
	/* @var $this PostController */
	/* @var $data_provider CActiveDataProvider */

	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/publishing.js'), CClientScript::POS_HEAD);

	$this->pageTitle = Yii::app()->name . ' - Посты';
?>

<div class = "panel panel-default">
	<p>
		<a href = "<?php echo $this->createUrl('post/create'); ?>"><button type
			= "button" class = "btn btn-primary pull-right">Создать новый пост
			</button></a>
	</p>
	<div class="clearfix"></div>
</div>

<div class = "table-responsive">
	<?php
		$this->widget('zii.widgets.grid.CGridView', array(
			'id' => 'post_list',
			'dataProvider' => $data_provider,
			'template' => '{items} {pager}',
			'selectableRows' => 0,
			'loadingCssClass' => 'wait',
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
					'header' => 'Опубликован',
					'template' => '{publish} {unpublish}',
					'buttons' => array(
						'publish' => array(
							'label' => '<span class = "glyphicon ' .
								'glyphicon-unchecked"></span>',
							'url' => '$this->grid->controller->createUrl(' .
								'"post/update", array("id" => $data->id))',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Опубликовать'),
							'click' => 'function() { return publishing(jQuery('
								. 'this).attr("href"), true); }',
							'visible' => '!$data->published'
						),
						'unpublish' => array(
							'label' => '<span class = "glyphicon ' .
								'glyphicon-check"></span>',
							'url' => '$this->grid->controller->createUrl(' .
								'"post/update", array("id" => $data->id))',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Снять с публикации'),
							'click' => 'function() { return publishing(jQuery('
								. 'this).attr("href"), false); }',
							'visible' => '$data->published'
						)
					)
				),
				array(
					'class' => 'CButtonColumn',
					'header' => 'Удалить',
					'template' => '{delete}',
					'deleteConfirmation' => 'Удалить пост?',
					'buttons' => array(
						'delete' => array(
							'label' => '<span class = "glyphicon ' .
								'glyphicon-trash"></span>',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Удалить пост')
						)
					)
				)
			),
			'itemsCssClass' => 'table',
			'pager' => array(
				'header' => '',
				'firstPageLabel' => '&lt;&lt;',
				'prevPageLabel' => '&lt;',
				'nextPageLabel' => '&gt;',
				'lastPageLabel' => '&gt;&gt;',
				'hiddenPageCssClass' => 'disabled',
				'selectedPageCssClass' => 'active',
				'htmlOptions' => array('class' => 'pagination')
			)
		));
	?>
</div>
