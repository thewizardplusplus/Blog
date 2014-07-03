<?php

class BackupController extends CController {
	public function __construct($id, $module = NULL) {
		parent::__construct($id, $module);
		$this->defaultAction = 'list';
	}

	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(
			array(
				'allow',
				'actions' => array('list', 'new', 'log'),
				'users' => array('admin')
			),
			array(
				'deny',
				'users' => array('*')
			)
		);
	}

	public function actionList() {
		$this->testBackupDirectory();

		$backups_path = __DIR__ . Constants::BACKUPS_RELATIVE_PATH;
		$filenames = scandir($backups_path);
		$backups = array();
		foreach ($filenames as $filename) {
			$filename = $backups_path . '/' . $filename;
			if (is_file($filename) and strtolower(pathinfo($filename,
				PATHINFO_EXTENSION)) == 'zip')
			{
				$backup = new stdClass();
				$backup->timestamp = date('d.m.Y H:i:s', filemtime($filename));
				$backup->size = filesize($filename);
				if ($backup->size > 1024 and $backup->size < 1024 * 1024) {
					$backup->size = round($backup->size / 1024, 2) . ' KB';
				} else if ($backup->size > 1024 * 1024 and $backup->size < 1024
					* 1024 * 1024)
				{
					$backup->size = round($backup->size / 1024 * 1024, 2) .
						' MB';
				} else {
					$backup->size = round($backup->size / 1024 * 1024 * 1024, 2)
						. ' GB';
				}
				$backup->link = '/backups/' . basename($filename);

				$backups[] = $backup;
			}
		}

		$data_provider = new CArrayDataProvider($backups, array(
			'keyField' => 'timestamp',
			'sort' => array(
				'attributes' => array('timestamp'),
				'defaultOrder' => array('timestamp' => CSort::SORT_DESC)
			)
		));

		$log_text = $this->getLog();
		$this->render('list', array(
			'data_provider' => $data_provider,
			'log_text' => $log_text
		));
	}

	public function actionNew() {
		$this->testBackupDirectory();

		$start = date_create();
		$result = $this->backup(__DIR__ . '/../../files');
		if (!$result) {
			throw new CException('Не удалось создать бекап.');
		}
		Yii::log(
			date_create()
				->diff($start)
				->format('Длительность создания последнего бекапа: %I:%S.'),
			'info',
			'backups'
		);

		$this->redirect(array('backup/list'));
	}

	public function actionLog() {
		echo $this->getLog();
	}

	private function testBackupDirectory() {
		if (!file_exists(__DIR__ . Constants::BACKUPS_RELATIVE_PATH)) {
			$result = mkdir(__DIR__ . Constants::BACKUPS_RELATIVE_PATH);
			if (!$result) {
				throw new CException('Не удалось создать директорию для ' .
					'бекапов.');
			}
		}
	}

	private function backup($path, $context = null) {
		if (is_null($context)) {
			$context = new stdClass();
			$context->base_path = $path;
			$context->backup_name = 'backup_' . date('Y-m-d-H-i-s');

			$context->archive = new ZipArchive();
			$result = $context->archive->open(
				__DIR__
					. Constants::BACKUPS_RELATIVE_PATH
					. '/'
					. $context->backup_name
				. '.zip',
				ZIPARCHIVE::CREATE
			);
			if ($result === true) {
				$temporary_filename =
					sys_get_temp_dir()
						. '/'
						. uniqid(rand(), true);
				$result = file_put_contents(
					$temporary_filename,
					$this->dumpDatabase()
				);
				if ($result !== false) {
					$result = $context->archive->addFile(
						$temporary_filename,
						$context->backup_name . '/database_dump.xml'
					);
					if ($result) {
						$result = $this->backup($path, $context);
					}
				}

				$context->archive->close();
			}

			return $result;
		} else {
			$files = scandir($path);
			$files = array_diff($files, array('.', '..'));
			foreach ($files as $file) {
				$full_path = $path . '/' . $file;
				if (is_file($full_path)) {
					$result = $context->archive->addFile(
						$full_path,
						$context->backup_name
							. str_replace($context->base_path, '', $full_path)
					);
				} else if (is_dir($full_path)) {
					$result = $this->backup($full_path, $context);
				} else {
					continue;
				}

				if (!$result) {
					return false;
				}
			}

			return true;
		}
	}

	private function dumpDatabase() {
		$posts_dump = '';
		$posts = Post::model()->findAll(array('order' => 'create_time'));
		foreach ($posts as $post) {
			$title = base64_encode($post->title);
			$create_time =
				date_create($post->create_time)->format('Y-m-d\TH:i:s');
			$modify_time =
				date_create($post->modify_time)->format('Y-m-d\TH:i:s');
			$text = base64_encode($post->text);
			$tags = base64_encode($post->tags);
			$published = !$post->published ? ' published = "false"' : '';

			$posts_dump .=
				"\t<post "
					. "create-time = \"$create_time\" "
					. "modify-time = \"$modify_time\""
					. "$published>\n"
					. "\t\t<title>$title</title>\n"
					. "\t\t<text>$text</text>\n"
					. "\t\t<tags>$tags</tags>\n"
				. "\t</post>\n";
		}

		return
			"<?xml version = \"1.0\" encoding = \"utf-8\"?>\n"
				. "<blog>\n"
					. "$posts_dump"
				. "</blog>\n";
	}

	private function getLog() {
		$log_text = file(
			__DIR__ . '/../runtime/backups.log',
			FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
		);
		if (!empty($log_text)) {
			$log_text = end($log_text);
			$date = substr($log_text, 0, 19);
			$date = preg_replace(
				';^(\d{4})/(\d\d)/(\d\d) ((\d\d):(\d\d):(\d\d));',
				'$3.$2.$1 $4',
				$date
			);
			$log_text = substr($log_text, 37);
			$log_text = preg_replace('/:/', ' (' . $date . '):', $log_text, 1);
		} else {
			$log_text = '';
		}

		return $log_text;
	}
}
