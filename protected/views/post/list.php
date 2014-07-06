<?php
	/* @var $this PostController */
	/* @var $data_provider CActiveDataProvider */

	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/searching.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/sorting.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/post_list.js'),
		CClientScript::POS_HEAD
	);

	$this->pageTitle = Yii::app()->name;
?>

<?php if (!empty($tags)) { ?>
	<p class = "panel panel-default note">
		Посты с <?= count($tags) > 1 ? 'тегами' : 'тегом' ?>:
		<?php foreach ($tags as $tag) { ?>
			<?php $shortcut_tags = array_diff($tags, array($tag)); ?>
			<span class = "label label-success">
				<?= $tag ?>
				<a
					class = "badge"
					href = "<?=
						!empty($shortcut_tags)
							? $this->createUrl(
								'post/list',
								array('tags' => implode(',', $shortcut_tags))
							)
							: $this->createUrl('post/list')
					?>">
					&times;
				</a>
			</span>
		<?php } ?>
	</p>
<?php } ?>

<div class = "panel panel-default">
	<div class = "row">
		<div class = "col-md-6">
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
				<a
					class = "input-group-addon clear-search-input-button"
					href = "#">
					<span class = "glyphicon glyphicon-remove"></span>
				</a>
			</div>
		</div>

		<div class = "col-md-6">
			<div class = "btn-group pull-right">
				<button
					class = "btn btn-default create-sort-button <?=
						$order == 'create'
							? 'active'
							: ''
					?>">
					Новые
				</button>
				<button
					class = "btn btn-default modify-sort-button <?=
						$order == 'modify'
							? 'active'
							: ''
					?>">
					Изменения
				</button>
			</div>
		</div>
	</div>
</div>

<?php
	$this->widget('zii.widgets.CListView', array(
		'id' => 'post-list',
		'dataProvider' => $data_provider,
		'template' => '{items} {pager}',
		'enableHistory' => true,
		'enableSorting' => false,
		'itemView' => '_view',
		'separator' => '<hr />',
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
			'htmlOptions' => array('class' => 'pager')
		),
		'pagerCssClass' => 'page-controller',
	));
?>

<script>
	var disqus_api_key = '<?= Constants::DISQUS_API_KEY ?>';
	var disqus_shortname = '<?= Constants::DISQUS_SHORTNAME ?>';
</script>
<?= CHtml::scriptFile(CHtml::asset('scripts/disqus_counters.js')) ?>
