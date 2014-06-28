<?php
	/* @var $this PostController */
	/* @var $data_provider CActiveDataProvider */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/searching.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/post_list.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name;
?>

<?php if (isset($_GET['tag'])) { ?>
	<p class = "panel panel-default note">
		Посты с тегом <span class = "label label-success"><?php echo CHtml::
			encode($_GET['tag']); ?></span>:
	</p>
<?php } ?>

<div class = "panel panel-default">
	<div class = "input-group">
		<span class = "input-group-addon">
			<span class = "glyphicon glyphicon-search"></span>
		</span>
		<input
			class = "form-control search-input"
			value = "<?=
				isset($_GET['search'])
					? CHtml::encode($_GET['search'])
					: ''
			?>" />
	</div>
</div>

<?php
	$this->widget('zii.widgets.CListView', array(
		'id' => 'post-list',
		'dataProvider' => $data_provider,
		'template' => '{items} {pager}',
		'enableHistory' => true,
		'itemView' => '_view',
		'loadingCssClass' => 'wait',
		'afterAjaxUpdate' => new CJavaScriptExpression(
			'function() {'
				. 'PostList.initialize();'
				. 'UpdateCommentsCounters();'
			. '}'
		),
		'emptyText' => 'Нет постов.',
		'pager' => array(
			'maxButtonCount' => 0,
			'header' => '',
			'prevPageLabel' => '&lt;&lt; Следующие',
			'nextPageLabel' => 'Предыдующие &gt;&gt;',
			'firstPageCssClass' => 'hidden',
			'lastPageCssClass' => 'hidden',
			'hiddenPageCssClass' => 'disabled',
			'htmlOptions' => array('class' => 'pagination')
		)
	));
?>

<script>
	var disqus_api_key = '<?= Constants::DISQUS_API_KEY ?>';
	var disqus_shortname = '<?= Constants::DISQUS_SHORTNAME ?>';
</script>
<?= CHtml::scriptFile(CHtml::asset('scripts/disqus_counters.js')) ?>
