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
	if (isset($_GET["search"]) or isset($_GET["tags"])) {
		$this->pageTitle .= ' - Результаты поиска ';
		if (isset($_GET["search"])) {
			$query_for_title = CHtml::encode($_GET["search"]);
			if (
				strlen($query_for_title)
				> Constants::MAXIMAL_LENGTH_SEARCH_QUERY_IN_TITLE
			) {
				$query_for_title = substr(
					$query_for_title,
					0,
					Constants::MAXIMAL_LENGTH_SEARCH_QUERY_IN_TITLE
				) . '...';
			}

			$this->pageTitle .= 'по запросу "' . $query_for_title . '"';
		}
		if (isset($_GET["tags"])) {
			if (isset($_GET["search"])) {
				$this->pageTitle .= ' и ';
			}

			$tags_for_title = explode(',', $_GET["tags"]);
			$tags_for_title = array_map(
				function($tag) {
					return CHtml::encode(trim($tag));
				},
				$tags_for_title
			);
			$tags_for_title = '"' . implode('", "', $tags_for_title) . '"';
			if (
				strlen($tags_for_title)
				> Constants::MAXIMAL_LENGTH_TAGS_LIST_IN_TITLE
			) {
				$tags_for_title = substr(
					$tags_for_title,
					0,
					Constants::MAXIMAL_LENGTH_TAGS_LIST_IN_TITLE
				) . '...';
			}

			$this->pageTitle .= 'по тегам ' . $tags_for_title;
		}
	} else {
		$this->pageTitle .= ' - Посты';
	}
	if (
		isset($_GET["sort"])
		and ($_GET["sort"] == 'create' or $_GET["sort"] == 'modify')
	) {
		if ($_GET["sort"] == 'create') {
			$this->pageTitle .= ' - Сортировка по дате создания';
		} else if ($_GET["sort"] == 'modify') {
			$this->pageTitle .= ' - Сортировка по дате изменения';
		}
	}
	if (isset($_GET["Post_page"]) and is_numeric($_GET["Post_page"])) {
		$this->pageTitle .= ' - Страница ' . $_GET["Post_page"];
	}
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
			<div class = "input-group pull-left">
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

<div class = "clearfix">
	<?php
		$this->widget('zii.widgets.CListView', array(
			'id' => 'post-list',
			'dataProvider' => $data_provider,
			'template' => '{items} {summary} {pager}',
			'enableHistory' => true,
			'enableSorting' => false,
			'itemView' => '_view',
			'loadingCssClass' => 'wait',
			'summaryCssClass' => 'summary pull-right',
			'afterAjaxUpdate' => new CJavaScriptExpression(
				'function() {'
					. 'PostList.initialize();'
				. '}'
			),
			'emptyText' => 'Нет постов.',
			'summaryText' => 'Посты {start}-{end} из {count}.',
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
		));
	?>
</div>
