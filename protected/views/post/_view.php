<?php
	/* @var $this PostController */
	/* @var $data Post */

	if (!empty($data->tags)) {
		$tags_list = '';
		foreach (array_map('trim', explode(',', $data->tags)) as $tag) {
			$tags_list .= CHtml::link($tag, $this->createUrl('post/list', array(
				'tag' => $tag)), array('class' => 'label label-success'));
		}
	}
?>

<article class = "panel panel-default">
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

	<?php if (!empty($data->tags)) { ?>
	<p>Теги: <?php echo $tags_list; ?></p>
	<?php } ?>

	<?php
		if ($this->action->id == 'list' and preg_match(Constants::
			CUT_TAG_PATTERN, $data->text))
		{
	?>
	<p class = "read-next">
		<?php echo CHtml::link('Читать дальше &gt;&gt;', $this->createUrl(
			'post/view', array('id' => $data->id, 'title' => $data->title)),
			array('class' => 'btn btn-default')); ?>
	</p>
	<?php } ?>
</article>
