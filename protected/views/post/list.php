<?php
	/* @var $this PostController */
	/* @var $data_provider CActiveDataProvider */

	$this->pageTitle = Yii::app()->name;
?>

<?php if (isset($_GET['tag'])) { ?>
	<p class = "panel panel-default note">
		Посты с тегом <span class = "label label-success"><?php echo CHtml::
			encode($_GET['tag']); ?></span>:
	</p>
<?php } ?>

<?php
	$this->widget('zii.widgets.CListView', array(
		'dataProvider' => $data_provider,
		'template' => '{items} {pager}',
		'itemView' => '_view',
		'loadingCssClass' => 'wait',
		'afterAjaxUpdate' => new CJavaScriptExpression(
			'function() {'
				. 'if (typeof UpdateCommentsCounters != "undefined") {'
					. 'UpdateCommentsCounters();'
				. '}'
			. '}'
		),
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
