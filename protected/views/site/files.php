<?php
	/* @var $this SiteController */

	Yii::app()->getClientScript()->registerCssFile(CHtml::asset('jquery-ui/css/'
		. 'theme/jquery-ui.min.css'));
	Yii::app()->getClientScript()->registerCssFile(CHtml::asset('elfinder/css/'
		. 'elfinder.min.css'));

	Yii::app()->getClientScript()->registerCoreScript('jquery');
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset('jquery-ui/'
		. 'js/jquery-ui.min.js'), CClientScript::POS_HEAD);
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset('elfinder/js'
		. '/elfinder.min.js'), CClientScript::POS_HEAD);
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset('elfinder/js'
		. '/i18n/elfinder.ru.js'), CClientScript::POS_HEAD);

	$this->pageTitle = Yii::app()->name . ' - Файлы';
?>

<div id = "elfinder"></div>
<div id = "path-dialog" title = "Путь к файлу:" style =
	"text-align: center; display: none;">
	<input readonly = "readonly" style = "width: 175px;" />
</div>

<script>
	jQuery('#path-dialog input').focus(function() {
		jQuery(this).select();
	});
	var elfinder = jQuery('#elfinder').elfinder({
		<?php //TODO: correct path on public server. ?>
		url:  '/web/blog/elfinder/php/connector.php',
		getFileCallback: function(path) {
			jQuery('#path-dialog input').val(path);
			jQuery('#path-dialog').dialog({
				width: 200,
				height: 52,
				modal: true,
				resizable: false,
				dialogClass: 'std42-dialog elfinder-dialog ' +
					'elfinder-dialog-active'
			});
		},
		lang: 'ru'
	}).elfinder('instance');
</script>
