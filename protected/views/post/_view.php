<?php
	/* @var $this PostController */
	/* @var $data Post */

	$is_in_list = $this->action->id == 'list';

	if (!empty($data->tags)) {
		$tags = array_map('trim', explode(',', $data->tags));

		$tags_list = '';
		for ($i = 0; $i < count($tags); $i++) {
			if ($i != 0) {
				$tags_list .= ', ';
			}

			$tag = $tags[$i];
			$tags_list .= CHtml::link($tag, $this->createUrl('post/list', array(
				'tag' => $tag)));
		}
	}
?>

<?php if ($is_in_list) { ?>
<div class = "view">
<?php } ?>
	<h2>
		<?php
			if ($is_in_list) {
				echo CHtml::link($data->title, $this->createUrl('post/view',
					array('id' => $data->id)));
			} else {
				echo $data->title;
			}
		?>
	</h2>

	<p style = "margin-left: 25px; font-size: smaller; color: #808080;">
		<?php echo $data->getAttributeLabel('create_time'); ?> <time><?php echo
			Post::formatTime($data->create_time); ?></time>.<br />
		<?php echo $data->getAttributeLabel('modify_time'); ?> <time><?php echo
			Post::formatTime($data->modify_time); ?></time>.
	</p>

	<p>
		<?php
			$this->beginWidget('CMarkdown', array('purifyOutput' => TRUE));
			echo Post::processText($is_in_list ? 'list:guest' : 'view', $data
				->text);
			$this->endWidget();
		?>
	</p>

	<?php if (!empty($data->tags)) { ?>
	<p>Теги: <?php echo $tags_list; ?>.</p>
	<?php } ?>

	<?php
		if ($is_in_list and preg_match(Post::CUT_TAG_PATTERN, $data->text)) {
	?>
	<p style = "text-align: right;">
		<?php echo CHtml::link('Читать дальше &gt;&gt;', $this->createUrl(
			'post/view', array('id' => $data->id))); ?>
	</p>
	<?php } ?>
<?php if ($is_in_list) { ?>
</div>
<?php } ?>
