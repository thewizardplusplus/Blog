<?php
	/* @var $this FileController */
	/* @var $data_provider CActiveDataProvider */
	/* @var $path string */

	Yii::app()->getClientScript()->registerCssFile(CHtml::asset(
		'jQueryFormStyler/jquery.formstyler.css'));
	Yii::app()->getClientScript()->registerCssFile(CHtml::asset(
		'styles/styler.css'));

	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'jQueryFormStyler/jquery.formstyler.min.js'), CClientScript::POS_HEAD);
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/files.js'), CClientScript::POS_HEAD);
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/styler.js'), CClientScript::POS_HEAD);

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
		'name' => 'files',
		'link' => $this->createUrl('file/list')
	));
?>

<div class = "panel panel-default">
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

<?php echo CHtml::beginForm($this->createUrl('file/upload', array('path' =>
	$path)), 'post', array('enctype' => 'multipart/form-data')); ?>

<div class = "panel panel-default">
	<fieldset>
		<legend>Загрузить файлы:</legend>

		<div class = "form-group">
			<?php echo CHtml::label('Файл:', 'file'); ?>
			<?php echo CHtml::fileField('file'); ?>
		</div>

		<?php echo CHtml::submitButton('Загрузить', array('class' => 'btn btn-'
			. 'primary')); ?>
	</fieldset>
</div>

<?php echo CHtml::endForm(); ?>

<div class = "table-responsive">
	<?php
		$this->widget('zii.widgets.grid.CGridView', array(
			'dataProvider' => $data_provider,
			'template' => '{items}',
			'hideHeader' => TRUE,
			'selectableRows' => 0,
			'columns' => array(
				array(
					'type' => 'raw',
					'value' => 'CHtml::link("<span class = \"file-icon ' .
						'glyphicon glyphicon-" . ($data->is_file ? "file" : ' .
						'($data->name != ".." ? "folder-open" : "arrow-up")) . '
						. '"\"></span>" . $data->name, $data->link, $data->' .
						'is_file ? array("target" => "_blank") : array())'
				),
				array(
					'class' => 'CButtonColumn',
					'template' => '{view} {rename} {remove}',
					'buttons' => array(
						'view' => array(
							'label' => '<span class = "glyphicon glyphicon-' .
								'search"></span>',
							'url' => '$data->link',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Ссылка на файл'),
							'click' => 'function() { return fileView(this); '
								. '}',
							'visible' => '$data->is_file'
						),
						'rename' => array(
							'label' => '<span class = "glyphicon glyphicon-' .
								'pencil"></span>',
							'url' => '$this->grid->controller->createUrl("file/'
								. 'rename", array("path" => "' . $path . '", ' .
								'"old_filename" => $data->name))',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Переименовать файл'),
							'click' => 'function() { return fileRename(this); '
								. '}',
							'visible' => '$data->is_file'
						),
						'remove' => array(
							'label' => '<span class = "glyphicon glyphicon-' .
								'trash"></span>',
							'url' => '$this->grid->controller->createUrl("file/'
								. 'remove", array("path" => "' . $path . '", ' .
								'"filename" => $data->name))',
							'imageUrl' => FALSE,
							'options' => array('title' => 'Удалить файл'),
							'click' => 'function () { return fileRemove(this); '
								. '}',
							'visible' => '$data->is_file'
						)
					),
					'htmlOptions' => array('class' => 'button-column wide')
				)
			),
			'itemsCssClass' => 'table'
		));
	?>
</div>

<div id = "file-path-dialog" class = "modal fade">
	<div class = "modal-dialog">
		<div class = "modal-content">
			<div class = "modal-header">
				<button type = "button" class = "close" data-dismiss = "modal"
					aria-hidden = "true">&times;</button>
				<h2 class = "modal-title">Путь к файлу</h2>
			</div>

			<div class = "modal-body">
				<?php echo CHtml::beginForm('#'); ?>
				<div class = "form-group">
					<?php echo CHtml::textField('file-path', '', array(
						'readonly' => 'readonly',
						'class' => 'form-control'
					)); ?>
				</div>
				<?php echo CHtml::endForm(); ?>
			</div>

			<div class = "modal-footer">
				<button type = "button" class = "btn btn-default" data-dismiss =
					"modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>
