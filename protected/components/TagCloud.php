<?php

	Yii::import('zii.widgets.CPortlet');

	class TagCloud extends CPortlet {
		protected function renderContent() {
			Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
				'js/jquery.tagcanvas.min.js'), CClientScript::POS_HEAD);
			Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
				'js/tag-cloud.js'), CClientScript::POS_HEAD);

			$tags = array();
			$number_of_posts = Post::model()->count();
			if (!empty($number_of_posts)) {
				$posts = Post::model()->findAll('`tags` <> ""');
				if (!empty($posts)) {
					foreach ($posts as $post) {
						$tags = array_merge($tags, array_map('trim', explode(
							',', $post->tags)));
					}

					$tags = array_map(function($item) use ($number_of_posts) {
						return round(100 * $item / $number_of_posts);
					}, array_count_values($tags));
				}
			}

			if (!empty($tags)) {
?>

<!-- Используется скрипт http://www.goat1000.com/tagcanvas.php -->
<div id = "tag-cloud-canvas-container">
	<canvas id = "tag-cloud-canvas" width = "190" height = "190">
		<p>Ваш браузер не поддерживает тег HTML5 &lt;canvas&gt;.</p>
	</canvas>
</div>

<div id = "tag-cloud-list">
	<ul>
		<?php foreach ($tags as $tag => $rate) { ?>
		<li><?php echo CHtml::link("$tag", $this->controller->createUrl(
			'post/list', array('tag' => $tag)), array('class' => 'tag ' .
			$this->getTagClassByRate($rate)));?></li>
		<?php } ?>
	</ul>
</div>

<?php
			}
		}

		private function getTagClassByRate($rate) {
			if ($rate < 20) {
				return 'rate1';
			} else if ($rate < 40) {
				return 'rate2';
			} else if ($rate < 60) {
				return 'rate3';
			} else if ($rate < 80) {
				return 'rate4';
			} else {
				return 'rate5';
			}
		}
	}
?>
