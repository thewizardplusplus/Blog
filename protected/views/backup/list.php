<?php
	/* @var $this BackupController */
	/* @var $data_provider CActiveDataProvider */

	$this->pageTitle = Yii::app()->name . ' - Бекапы';
?>

<div class = "table-responsive">
	<?php
		$this->widget(
			'zii.widgets.grid.CGridView',
			array(
				'id' => 'backup-list',
				'dataProvider' => $data_provider,
				'template' => '{items} {pager}',
				'selectableRows' => 0,
				'columns' => array(
					array(
						'name' => 'Время создания',
						'type' => 'raw',
						'value' => '"<time>" . $data->timestamp . "</time>"'
					),
					array(
						'name' => 'Размер',
						'value' => '$data->size'
					),
					array(
						'class' => 'CButtonColumn',
						'header' => 'Скачать',
						'template' => '{download}',
						'buttons' => array(
							'download' => array(
								'label' => '<span class = "glyphicon glyphicon-'
									. 'download-alt"></span>',
								'url' => '$data->link',
								'imageUrl' => FALSE,
								'options' => array('title' => 'Скачать')
							)
						)
					)
				),
				'itemsCssClass' => 'table',
				'loadingCssClass' => 'wait',
				'emptyText' => 'Нет бекапов.',
				'pager' => array(
					'header' => '',
					'firstPageLabel' => '&lt;&lt;',
					'prevPageLabel' => '&lt;',
					'nextPageLabel' => '&gt;',
					'lastPageLabel' => '&gt;&gt;',
					'selectedPageCssClass' => 'active',
					'hiddenPageCssClass' => 'disabled',
					'htmlOptions' => array('class' => 'pagination')
				),
				'pagerCssClass' => 'page-controller'
			)
		);
	?>
</div>

<?php if (!empty($log_text)) { ?>
<pre class = "log"><?php echo $log_text; ?></pre>
<?php } ?>
