<?php
	/* @var $this PostController */
	/* @var $data Post */

	$post_tags = array();
	if (!empty($data->tags)) {
		$post_tags = array_map('trim', explode(',', $data->tags));
	}
	$query_tags = array();
	if (!empty($_GET['tags'])) {
		$query_tags = array_map('trim', explode(',', $_GET['tags']));
	}

	if ($this->action->id == 'view') {
		Yii::app()->getClientScript()->registerMetaTag(
			Post::processDescription($data->text),
			'description'
		);
		Yii::app()->getClientScript()->registerMetaTag(
			implode(', ', $post_tags),
			'keywords'
		);

		Yii::app()->getClientScript()->registerScriptFile(
			CHtml::asset('scripts/post_view.js'),
			CClientScript::POS_HEAD
		);
	}
?>

<article
	class = "panel panel-default<?=
		$this->action->id == 'view'
			? ' post-view'
			: ''
	?>">
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
		</div>
	<?php } ?>
</article>
