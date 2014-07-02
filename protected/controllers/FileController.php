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
				return strnatcmp($a->name, $b->name);
			}
		});

		$data_provider = new CArrayDataProvider($files, array(
			'keyField' => 'name',
			'pagination' => FALSE
		));

		$exists_files = array_diff($filenames, array('..'));
		$exists_files = array_filter(
			$exists_files,
			function($exists_file) use($base_path) {
				return is_file($base_path . '/' . $exists_file);
			}
		);

		$this->render(
			'list',
			array(
				'data_provider' => $data_provider,
				'path' => $path,
				'exists_files' => $exists_files
			)
		);
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
		$full_path = $base_path . '/' . $path;

		$exists_files = scandir($full_path);
		$exists_files = array_diff($exists_files, array('.', '..'));
		$exists_files = array_filter(
			$exists_files,
			function($exists_file) use($full_path) {
				return is_file($full_path . '/' . $exists_file);
			}
		);

		$uploads_files = CUploadedFile::getInstancesByName('files');
		$collisions = array();
		foreach($uploads_files as $file) {
			if (in_array($file->name, $exists_files)) {
				$collisions[] = $file->name;
			}
		}
		if (!empty($collisions)) {
			throw new CHttpException(
				500,
				(count($collisions) > 1
					? 'Эти файлы'
					: 'Этот файл')
					. ' уже есть в текущей директории: «'
					. implode('», «', $collisions)
					. '».'
			);
		}

		foreach($uploads_files as $file) {
			if ($file->hasError) {
				throw new CHttpException(
					500,
					'Не удалось загрузить файл «'
					. $file->name
					. '».'
				);
			}

			$file_path = $full_path . '/' . $file->name;
			$result = $file->saveAs($file_path);
			if (!$result) {
				throw new CHttpException(
					500,
					'Не удалось загрузить файл «'
						. $file->name
						. '».'
				);
			}

			$image_type = exif_imagetype($file_path);
			if (
				$image_type == IMAGETYPE_PNG
				or $image_type == IMAGETYPE_JPEG
				or $image_type == IMAGETYPE_GIF
			) {
				try {
					$image = new SimpleImage($file_path);
					$maximal_width_of_images =
						Parameters::get()->maximal_width_of_images;
					if ($image->get_width() > $maximal_width_of_images) {
						$image->fit_to_width($maximal_width_of_images)->save();
					}
				} catch (Exception $exception) {
					Yii::log($exception->getMessage());
				}
			}
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
