<?php

Yii::import('zii.widgets.CPortlet');

class TagCloud extends CPortlet {
	protected function renderContent() {
		$command = Yii::app()->db->createCommand('SELECT `{{posts}}`.`tags` ' .
			'FROM `{{posts}}` WHERE `{{posts}}`.`tags` <> ""');
		$results = $command->queryAll();

		$all_tags = array();
		foreach ($results as $tag) {
			$all_tags = array_merge($all_tags, array_map('trim', explode(',',
				$tag['tags'])));
		}

		$result = '';
		foreach (array_count_values($all_tags) as $tag => $count) {
			if (!empty($result)) {
				$result .= ' ';
			}
			$result .= CHtml::link("$tag ($count)", $this->controller->
				createUrl('post/list', array('tag' => $tag)));
		}

		echo $result;
	}
}
