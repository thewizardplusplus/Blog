<?php

	Yii::import('zii.widgets.CPortlet');

	class TagCloud extends CPortlet {
		protected function renderContent() {
			Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
				'js/jquery.tagcanvas.min.js'), CClientScript::POS_HEAD);
			Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
				'js/tag-cloud.js'), CClientScript::POS_HEAD);

			$command = Yii::app()->db->createCommand('SELECT `{{posts}}`.`tags`'
				. ' FROM `{{posts}}` WHERE `{{posts}}`.`tags` <> ""');
			$results = $command->queryAll();

			$all_tags = array();
			foreach ($results as $tag) {
				$all_tags = array_merge($all_tags, array_map('trim', explode(
					',', $tag['tags'])));
			}

			$all_tags = array_count_values($all_tags);
			$all_tags = array_map(function($item) {
				return $item * 10;
			}, $all_tags);
?>

<!-- Используется скрипт http://www.goat1000.com/tagcanvas.php -->
<div id = "tag-cloud-canvas-container">
	<canvas id = "tag-cloud-canvas" width = "190" height = "190">
		<p>Ваш браузер не поддерживает тег HTML5 &lt;canvas&gt;.</p>
	</canvas>
</div>

<div id = "tag-cloud-list">
	<ul>
		<?php foreach ($all_tags as $tag => $count) { ?>
		<li><?php echo CHtml::link("$tag", $this->controller->createUrl(
			'post/list', array('tag' => $tag)), array('style' => 'font-size: ' .
			$count . 'px;'));?></li>
		<?php } ?>
	</ul>
</div>

<?php
		}
	}
?>
