<?php

class FileController extends CController {
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
				'actions' => array('list', 'rename', 'remove', 'upload'),
				'users' => array('admin')
			),
			array(
				'deny',
				'users' => array('*')
			)
		);
	}

	public function actionList() {
		$this->testFilesDirectory();

		$base_path = __DIR__ . Constants::FILES_RELATIVE_PATH;
		$path = $this->getPath($base_path);
		$base_path .= '/' . $path;
		$filenames = array_diff(scandir($base_path), array('.'));
		if (empty($path)) {
			$filenames = array_diff($filenames, array('..'));
		}
		$files = array();
		$counter = 0;
		foreach ($filenames as $filename) {
			$file = new stdClass;
			$file->id = $counter++;
			$file->name = $filename;

			$full_path = $base_path . '/' . $filename;
			$file->is_file = is_file($full_path);
			if ($file->is_file) {
				$file->link = $path . '/' . $filename;
			} else {
				$new_path = $path;
				if ($filename != '..') {
					if (!empty($new_path)) {
						$new_path .= '/';
					}
					$new_path .= $filename;
				} else {
					$index = strpos($new_path, '/');
					if ($index !== FALSE) {
						$new_path = substr($new_path, 0, $index);
					} else {
						$new_path = '';
					}
				}

				if (!empty($new_path)) {
					$file->link = $this->createUrl('file/list', array('path' =>
						$new_path));
				} else {
					$file->link = $this->createUrl('file/list');
				}
			}

			$files[] = $file;
		}
		usort($files, function($a, $b) {
			if ($a->is_file xor $b->is_file) {
				return $a->is_file ? 1 : -1;
			} else {
				return strcmp($a->name, $b->name);
			}
		});

		$data_provider = new CArrayDataProvider($files, array(
			'keyField' => 'name',
			'pagination' => FALSE
		));

		$this->render('list', array(
			'data_provider' => $data_provider,
			'path' => $path
		));
	}

	public function actionRename($old_filename) {
		if (!isset($_POST['new_filename'])) {
			throw new CException('Неверные параметры действия file/rename.');
		}

		$base_path = __DIR__ . Constants::FILES_RELATIVE_PATH;
		$path = $this->getPath($base_path);
		$base_path .= '/' . $path . '/';
		$new_filename = $_POST['new_filename'];
		$result = rename($base_path . $old_filename, $base_path .
			$new_filename);
		if (!$result) {
			throw new CException('Не удалось переименовать файл.');
		}

		if (Yii::app()->request->isAjaxRequest) {
			echo $new_filename;
		} else {
			if (!empty($path)) {
				$this->redirect(array('list', 'path' => $path));
			} else {
				$this->redirect(array('list'));
			}
		}
	}

	public function actionRemove($filename) {
		$base_path = __DIR__ . Constants::FILES_RELATIVE_PATH;
		$path = $this->getPath($base_path);
		$result = unlink($base_path . '/' . $path . '/' . $filename);
		if (!$result) {
			throw new CException('Не удалось удалить файл.');
		}

		if (!empty($path)) {
			$this->redirect(array('list', 'path' => $path));
		} else {
			$this->redirect(array('list'));
		}
	}

	public function actionUpload() {
		$base_path = __DIR__ . Constants::FILES_RELATIVE_PATH;
		$path = $this->getPath($base_path);
		$file = CUploadedFile::getInstanceByName('file');
		if ($file->hasError) {
			throw new CException('Не удалось загрузить файл.');
		}

		$result = $file->saveAs($base_path . '/' . $path . '/' . $file->name);
		if (!$result) {
			throw new CException('Не удалось загрузить файл.');
		}

		if (!empty($path)) {
			$this->redirect(array('list', 'path' => $path));
		} else {
			$this->redirect(array('list'));
		}
	}

	private function testFilesDirectory() {
		$path_to_files_root_diectory = __DIR__ . Constants::FILES_RELATIVE_PATH;
		if (!file_exists($path_to_files_root_diectory)) {
			$result = mkdir($path_to_files_root_diectory);
			if (!$result) {
				throw new CException('Не удалось создать корневую директорию ' .
					'для файлов.');
			}
		}

		$path_to_files_diectory = $path_to_files_root_diectory . '/files';
		if (!file_exists($path_to_files_diectory)) {
			$result = mkdir($path_to_files_diectory);
			if (!$result) {
				throw new CException('Не удалось создать директорию для ' .
					'файлов.');
			}
		}

		$path_to_images_diectory = $path_to_files_root_diectory . '/images';
		if (!file_exists($path_to_images_diectory)) {
			$result = mkdir($path_to_images_diectory);
			if (!$result) {
				throw new CException('Не удалось создать директорию для ' .
					'изображений.');
			}
		}
	}

	private function getPath($base_path) {
		$path = '';
		if (isset($_GET['path']) and !empty($_GET['path'])) {
			if (!preg_match('/^(files|images)/', $_GET['path']) or preg_match(
					'/\/\.\.\/|^\.\.\/|\/\.\.$|^\.\.$/', $_GET['path']))
			{
				throw new CException('Неверный путь.');
			}

			$path = $_GET['path'];
			if (!is_dir($base_path . '/' . $path)) {
				throw new CException('Неверный путь.');
			}
		}

		return $path;
	}
}
