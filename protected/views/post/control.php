<?php
	/* @var $this PostController */
	/* @var $data_provider CActiveDataProvider */

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
					'header' => 'Действия',
					'buttons' => array(
						'view' => array('visible' => 'FALSE'),
						'update' => array('visible' => 'FALSE'),
						'delete' => array(
							'label' => '<span class = ' .
								'"glyphicon glyphicon-trash"></span>',
							'imageUrl' => FALSE,
							'options' => array(
								'title' => 'Удалить пост',
								'style' => 'font-size: larger;'
							)
						)
					)
				),
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
