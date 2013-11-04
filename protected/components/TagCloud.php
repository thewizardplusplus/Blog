<?php

Yii::import('zii.widgets.CPortlet');

class TagCloud extends CPortlet {
	protected function renderContent() {
		$tags = array();
		$number_of_posts = Post::model()->count('published = 1');
		if (!is_null($number_of_posts) and $number_of_posts != 0) {
			$posts = Post::model()->findAll('`tags` <> "" AND published = 1');
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
			foreach ($tags as $tag => $rate) {
				if ($rate < 20) {
					$rate = 'rate1';
				} else if ($rate < 40) {
					$rate = 'rate2';
				} else if ($rate < 60) {
					$rate = 'rate3';
				} else if ($rate < 80) {
					$rate = 'rate4';
				} else {
					$rate = 'rate5';
				}
				echo CHtml::link("$tag", $this->controller->createUrl(
					'post/list', array('tag' => $tag)), array('class' =>
					'label label-success tag ' . $rate));
			}

			echo '<div class = "clearfix"></div>';
		}
	}
}
