<?php
	/* @var $this PostController */
	/* @var $data Post */

	if ($this->action->id == 'view') {
		Yii::app()->getClientScript()->registerScriptFile(
			CHtml::asset('scripts/post_view.js'),
			CClientScript::POS_HEAD
		);

		Yii::app()->getClientScript()->registerScript(
			uniqid(rand(), true),
			'var addthis_config = { ui_language: "ru" };'
				. 'var addthis_share = {'
					. 'title: "' . CHtml::encode($data->title) . '",'
					. 'templates: {'
						. 'twitter: "{{title}}: {{url}}"'
					. '}'
				. '}',
			CClientScript::POS_HEAD
		);
		Yii::app()->getClientScript()->registerScriptFile(
			'http://s7.addthis.com/js/300/addthis_widget.js#pubid='
				. Constants::ADDTHIS_PROFILE_ID,
			CClientScript::POS_HEAD
		);
	}

	$post_tags = array();
	if (!empty($data->tags)) {
		$post_tags = array_map('trim', explode(',', $data->tags));
	}
	$query_tags = array();
	if (!empty($_GET['tags'])) {
		$query_tags = array_map('trim', explode(',', $_GET['tags']));
	}
?>

<article>
	<?php if (!Yii::app()->user->isGuest) { ?>
		<?= CHtml::link(
			'<span class = "glyphicon glyphicon-trash"></span>',
			$this->createUrl(
				'post/delete',
				array('id' => $data->id)
			),
			array(
				'class' => 'btn btn-default pull-right delete-post-button',
				'title' => 'Удалить',
				'data-post-title' => $data->title,
				'data-redirect-url' => $this->createUrl('post/list'),
				'data-processing-icon' =>
					Yii::app()->request->baseUrl
					. '/images/processing-icon.gif'
			)
		) ?>
		<?= CHtml::link(
			'<span class = "glyphicon glyphicon-pencil"></span>',
			$this->createUrl(
				'post/update',
				array('id' => $data->id)
			),
			array(
				'class' => 'btn btn-default pull-right edit-post-button',
				'title' => 'Редактировать'
			)
		) ?>
		<?php if ($data->published) { ?>
			<?= CHtml::link(
				'<span class = "glyphicon glyphicon-eye-open"></span>',
				$this->createUrl(
					'post/update',
					array('id' => $data->id)
				),
				array(
					'class' =>
						'btn btn-default pull-right publishing-post-button',
					'title' => 'Скрыть',
					'data-published' => 'false',
					'data-processing-icon' =>
						Yii::app()->request->baseUrl
						. '/images/processing-icon.gif'
				)
			) ?>
		<?php } else { ?>
			<?= CHtml::link(
				'<span class = "glyphicon glyphicon-eye-close"></span>',
				$this->createUrl(
					'post/update',
					array('id' => $data->id)
				),
				array(
					'class' =>
						'btn btn-default pull-right publishing-post-button',
					'title' => 'Опубликовать',
					'data-published' => 'true',
					'data-processing-icon' =>
						Yii::app()->request->baseUrl
						. '/images/processing-icon.gif'
				)
			) ?>
		<?php } ?>
	<?php } ?>

	<h2>
		<?php
			if ($this->action->id == 'list') {
				echo CHtml::link($data->title, $this->createUrl('post/view',
					array('id' => $data->id, 'title' => $data->title)));
			} else {
				echo $data->title;
			}
		?>
	</h2>

	<p class = "time">
		<?php echo $data->getAttributeLabel('create_time'); ?> <time><?php echo
			Post::formatTime($data->create_time); ?></time>.<br />
		<?php echo $data->getAttributeLabel('modify_time'); ?> <time><?php echo
			Post::formatTime($data->modify_time); ?></time>.
	</p>

	<?= Post::processText(
		$this->action->id == 'list' ? 'list' : 'view',
		$data->text
	) ?>

	<?php if (!empty($post_tags)) { ?>
		<p>
			Теги:
			<?php foreach ($post_tags as $post_tag) { ?>
				<?php if (!in_array($post_tag, $query_tags)) { ?>
					<a
						class = "label label-success"
						href = "<?= $this->createUrl(
							'post/list',
							array(
								'tags' => implode(
									',',
									array_merge($query_tags, array($post_tag))
								)
							)
						) ?>">
						<?= $post_tag ?>
					</a>
				<?php } else { ?>
					<span class = "label label-success"><?= $post_tag ?></span>
				<?php } ?>
			<?php } ?>
		</p>
	<?php } ?>

	<?php if ($this->action->id == 'list') { ?>
		<div class = "clearfix">
			<?php
				if (preg_match(Constants::CUT_TAG_PATTERN, $data->text)) {
			?>
				<?= CHtml::link(
					'Читать дальше &gt;&gt;',
					$this->createUrl(
						'post/view',
						array('id' => $data->id, 'title' => $data->title)
					),
					array('class' => 'btn btn-default pull-right')
				) ?>
			<?php } ?>
			<?= CHtml::link(
				'Комментарии',
				$this->createUrl(
					'post/view',
					array(
						'id' => $data->id,
						'title' => $data->title,
						'#' => 'disqus_thread'
					)
				),
				array(
					'class' => 'btn btn-default pull-left',
					'data-disqus-identifier' => $data->id
				)
			) ?>
		</div>
	<?php } else { ?>
		<div
			class = "addthis_toolbox addthis_default_style addthis_32x32_style">
			<a class = "addthis_button_facebook"></a>
			<a class = "addthis_button_vk"></a>
			<a class = "addthis_button_google_plusone_share"></a>
			<a
				class = "addthis_button_twitter"
				addthis:url = "<?= $this->createAbsoluteUrl(
					'post/view',
					array('id' => $data->id)
				) ?>">
			</a>
		</div>

		<div id = "disqus_thread"></div>
		<script>
			var disqus_shortname = '<?= Constants::DISQUS_SHORTNAME ?>';
			var disqus_identifier = <?= $data->id ?>;
			var disqus_title = '<?= CHtml::encode($data->title) ?>';
			var disqus_url = '<?= $this->createAbsoluteUrl(
				'post/view',
				array(
					'id' => $data->id,
					'title' => $data->title
				)
			) ?>';
		</script>
		<?= CHtml::scriptFile(CHtml::asset('scripts/disqus_thread.js')) ?>
	<?php } ?>
</article>
