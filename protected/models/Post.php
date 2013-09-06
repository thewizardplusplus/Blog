<?php

class Post extends CActiveRecord {
	const MAXIMAL_LENGTH_OF_TITLE_FIELD =    255;
	const CUT_TAG_PATTERN =                  '/<cut\s*\/>/';
	const MAXIMAL_LENGTH_OF_DISPLAYED_TEXT = 100;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public static function formatTime($time) {
		$parts = explode(' ', $time);
		return implode('.', array_reverse(explode('-', $parts[0]))) . ' ' .
			$parts[1];
	}

	public static function processText($view, $text) {
		switch ($view) {
			case 'list:guest':
				$result = preg_match(Post::CUT_TAG_PATTERN, $text, $matches,
					PREG_OFFSET_CAPTURE);
				if ($result) {
					$text = substr($text, 0, $matches[0][1]);
				}
				break;
			case 'list:admin':
				$text = trim(preg_replace('/\s{2,}/', ' ', Post::processText(
					'list:guest', $text)));
				if (mb_strlen($text, 'utf-8') > Post::
					MAXIMAL_LENGTH_OF_DISPLAYED_TEXT)
				{
					$text = mb_substr($text, 0, Post::
						MAXIMAL_LENGTH_OF_DISPLAYED_TEXT, 'utf-8') . '...';
				}
				break;
			case 'view':
				$text = preg_replace(Post::CUT_TAG_PATTERN, '', $text);
				break;
		}

		return $text;
	}

	public function tableName() {
		return '{{posts}}';
	}

	public function rules() {
		return array(
			array('title, text', 'required'),
			array('title', 'length', 'max' => Post::
				MAXIMAL_LENGTH_OF_TITLE_FIELD),
			array('tags', 'safe')
		);
	}

	public function attributeLabels() {
		return array(
			'title' => 'Заголовок:',
			'text' => 'Текст:',
			'create_time' => 'Дата создания:',
			'modify_time' => 'Дата изменения:',
			'tags' => 'Теги:'
		);
	}

	protected function beforeSave() {
		$result = parent::beforeSave();
		if ($result) {
			$current_time = date("Y-m-d H:i:s");
			if ($this->isNewRecord) {
				$this->create_time = $current_time;
			}
			$this->modify_time = $current_time;

			return true;
		} else {
			return false;
		}
	}
}
