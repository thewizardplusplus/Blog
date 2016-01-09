<?php
	/* @var $this FileController */
	/* @var $data_provider CActiveDataProvider */
	/* @var $path string */
	/* @var $exists_files array */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/jquery.jeditable.min.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/purl.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/files.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScript(
		uniqid(rand(), true),
		'FileList.setExistsFiles(['
		. (!empty($exists_files)
			? '"' . implode('", "', $exists_files) . '"'
			: '')
		. ']);',
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name . ' - Файлы';

	$path_parts = explode('/', $path);
	$path_last_item = basename($path);
	$path_items = array();
	$new_path = '';
	foreach ($path_parts as $item) {
		if ($item != $path_last_item) {
			if (!empty($new_path)) {
				$new_path .= '/';
			}
			$new_path .= $item;

			$path_items[] = array(
				'name' => $item,
				'link' => $this->createUrl('file/list', array('path' =>
					$new_path))
			);
		} else {
			$path_items[] = array(
				'name' => $item,
				'link' => ''
			);
		}
	}
	array_unshift($path_items, array(
		'name' => 'media',
		'link' => $this->createUrl('file/list')
	));
?>

<header class = "page-header visible-xs">
	<h4>Файлы</h4>
</header>

<div class = "with-top-margin">
	Текущий путь:
	<ol class = "breadcrumb">
		<?php foreach ($path_items as $item) { ?>
		<?php if (!empty($item['link'])) { ?>
		<li>
			<a href = "<?php echo $item['link']; ?>"><?php echo $item['name'];
				?></a>
		</li>
		<?php } else { ?>
		<li class = "active"><?php echo $item['name']; ?></li>
		<?php } ?>
		<?php } ?>
	</ol>
</div>

<?= CHtml::beginForm(
	$this->createUrl('file/upload', array('path' => $path)),
	'post',
	array('id' => 'file-upload', 'enctype' => 'multipart/form-data')
) ?>

<div class = "panel panel-default">
	<fieldset>
		<legend>Загрузить файлы:</legend>

		<div class = "form-group">
			<?php echo CHtml::label('Файл:', 'files'); ?>
			<?= CHtml::fileField(
				'files[]',
				'',
				array('multiple' => 'multiple')
			) ?>
			<div class = "alert alert-danger file-upload-error-view">
			</div>
		</div>

		<?php echo CHtml::submitButton('Загрузить', array('class' => 'btn btn-'
			. 'primary')); ?>
	</fieldset>
</div>

<?= CHtml::endForm() ?>

<div class = "table-responsive">
	<?php
		$this->widget(
			'zii.widgets.grid.CGridView',
			array(
				'id' => 'file-list',
				'dataProvider' => $data_provider,
				'template' => '{items}',
				'hideHeader' => TRUE,
				'selectableRows' => 0,
				'columns' => array(
					array(
						'type' => 'raw',
						'value' =>
							'"<a '
								. 'href = \"" . $data->link . "\""'
								. '. ($data->is_file'
									. '? " target = \"_blank\""'
									. ': ""'
								. ') . ">'
								. '<span '
									. 'class = \"'
										. 'glyphicon '
										. 'glyphicon-" . ('
											. '$data->is_file'
												. '? "file"'
												. ': ('
													. '$data->name != ".."'
														. '? "folder-open"'
														. ': "arrow-up"'
												. ')'
											. ') . "\">'
								. '</span>'
							. '</a>"',
						'htmlOptions' => array('class' => 'icon-column')
					),
					array(
						'type' => 'raw',
						'value' =>
							'$data->is_file'
								. '? "<span '
									. 'id = \"file-item" . $data->id . "\" '
									. 'class = \"file-item\" '
									. 'data-update-url = \""'
										. '. $this'
											. '->grid'
											. '->controller'
											. '->createUrl('
												. '"file/rename",'
												. 'array('
													. '"path" => "'
														. $path
														. '",'
													. '"old_filename" => '
														. '$data->name'
												. ')'
										. ') . "\" '
									. 'data-saving-icon-url = \""'
										. '. Yii::app()->request->baseUrl .'
										. '"/images/processing-icon.gif\">"'
										. '. $data->name .'
								. '"</span>"'
								. ': "<a href = \"" . $data->link . "\">"'
									. '. $data->name .'
									. '"</a>"'
					),
					array(
						'class' => 'CButtonColumn',
						'template' => '{rename} {remove}',
						'buttons' => array(
							'rename' => array(
								'label' =>
									'<span '
										. 'class = "'
											. 'glyphicon '
											. 'glyphicon-pencil'
										. '">'
									. '</span>',
								'url' =>
									'$this->grid->controller->createUrl('
										. '"file/rename",'
										. 'array('
											. '"path" => "' . $path . '",'
											. '"old_filename" => $data->name,'
											. '"file_id" => $data->id'
										. ')'
									. ')',
								'imageUrl' => FALSE,
								'options' => array(
									'title' => 'Переименовать файл'
								),
								'click' =>
									'function() {'
										. 'return FileList.rename(this);'
									. '}',
								'visible' => '$data->is_file'
							),
							'remove' => array(
								'label' =>
									'<span '
										. 'class = "glyphicon glyphicon-trash">'
									. '</span>',
								'url' =>
									'$this->grid->controller->createUrl('
										.'"file/remove",'
										. 'array('
											. '"path" => "' . $path . '",'
											. '"filename" => $data->name'
										. ')'
									. ')',
								'imageUrl' => FALSE,
								'options' => array('title' => 'Удалить файл'),
								'click' =>
									'function() {'
										. 'return FileList.removing(this);'
									. '}',
								'visible' => '$data->is_file'
							)
						)
					)
				),
				'itemsCssClass' => 'table table-striped',
				'loadingCssClass' => 'wait',
				'afterAjaxUpdate' => 'function() { FileList.initialize(); }'
			)
		);
	?>
</div>
