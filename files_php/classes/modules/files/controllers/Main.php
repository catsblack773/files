<?php
namespace modules\files\controllers;

class Main extends \modules\files\models\Main
{
	protected $config = [];
	protected $settings = [];

	public function __construct() {
		if (is_file(dirname(__DIR__).'/config.php')) {
			$this->config = include dirname(__DIR__).'/config.php';
			$this->settings = $this->config['settings'];
			unset($this->config['settings']);
		}
	}

	public function openFolderAction() {
		$path = \Apps::request()->post('path');
		if (!$path) {
			\Apps::response()->setData([
				'res' => false,
				'message' => 'Отсутствует переменая path'
			]);
			return true;
		}

		$folders = [];
		$files = [];

		$res = $this->openFolderList($folders, $files, $path);
		if ($res === false) {
			return true;
		}

		\Apps::response()->setData([
			'res' => true,
			'folders' => $folders,
			'files' => $files,
			'path' => $path
		]);
		return true;
	}

	public function comeBackAction() {
		$path = \Apps::request()->post('path');
		if (!$path) {
			\Apps::response()->setData([
				'res' => false,
				'message' => 'Отсутствует переменая path'
			]);
			return true;
		}

		$path = dirname($path);

		$folders = [];
		$files = [];

		$res = $this->openFolderList($folders, $files, $path);
		if ($res === false) {
			return true;
		}

		\Apps::response()->setData([
			'res' => true,
			'folders' => $folders,
			'files' => $files,
			'path' => $path
		]);
		return true;
	}

	public function uploadAction() {
		return true;
	}

	public function createFolderAction() {
		$post = \Apps::request()->post();
		$path = $post['path'];
		$fullPath = realpath(ROOT.$path);
		$folder = $post['folder'];
		
		if (is_dir($fullPath)) {
			$newDir = $fullPath.'/'.$folder;
			if (is_dir($newDir)) {
				\Apps::response()->setData([
					'res' => false,
					'message' => 'Папка "'.$folder.'" уже существует'
				]);
				return true;
			} else {
				mkdir($newDir, 0700, true);
				$folders = [];
				$files = [];

				$res = $this->openFolderList($folders, $files, $path);
				if ($res === false) {
					return true;
				}
				\Apps::response()->setData([
					'res' => true,
					'folders' => $folders,
					'files' => $files,
					'path' => $path,
					'message' => 'Папка "'.$folder.'" создана'
				]);
				return true;
			}
		}

		\Apps::response()->setData([
			'res' => false,
			'message' => 'Папка по пути "'.$path.'" отсутствует'
		]);
		return true;
	}

	public function removeAction() {
		$post = \Apps::request()->post();
		if (!isset($post['pathFrom']) && empty($post['pathFrom'])) {
			\Apps::response()->setData([
				'res' => false,
				'message' => 'Отсутствует переменая path'
			]);
			return true;
		}

		$paths = explode(',', $post['pathFrom']);
		foreach ($paths as $key => $path) {
			$this->unlinkDir(ROOT.$path);
		}

		$folders = [];
		$files = [];

		$res = $this->openFolderList($folders, $files, $post['pathTo']);
		if ($res === false) {
			return true;
		}

		\Apps::response()->setData([
			'res' => true,
			'folders' => $folders,
			'files' => $files,
			'path' => $post['pathTo'],
			'message' => 'Успешно удалено'
		]);
		return true;
	}

	public function renameAction() {
		\Apps::response()->setData([
			'res' => true
		]);
		return true;
	}

	public function repathAction() {
		\Apps::response()->setData([
			'res' => true
		]);
		return true;
	}

	public function zipAction() {
		\Apps::response()->setData([
			'res' => true
		]);
		return true;
	}

	public function updateFilesAction() {
		$path = \Apps::request()->post('path');
		$fullPath = realpath(ROOT.$path);
		$file = \Apps::request()->files('file');
		if (!$path || !is_dir($fullPath)) {
			\Apps::response()->setData([
				'res' => false,
				'message' => 'Отсутствует путь загрузки'
			]);
			return true;
		}

		if (!$file) {
			\Apps::response()->setData([
				'res' => false,
				'message' => 'Файл для загрузки отсутствует'
			]);
			return true;
		}

		$handle = new \Verot\Upload\Upload($file);
		if ($handle->uploaded) {
			$handle->file_new_name_body = md5($file['name']);
			$handle->process($fullPath);
			if ($handle->processed) {
				if ($handle->file_is_image) {
					$this->updateImage($handle, $fullPath);
				}
				$handle->clean();
				$folders = [];
				$files = [];

				if (!$this->openFolderList($folders, $files, $path)) {
					\Apps::response()->setData([
						'res' => false,
						'message' => 'Ошибка получение директории'
					]);
					return true;
				}
				\Apps::response()->setData([
					'res' => true,
					'folders' => $folders,
					'files' => $files,
					'path' => $path,
					'message' => 'Успешно загружено'
				]);
				return true;
				
			} else {
				\Apps::response()->setData([
					'res' => false,
					'message' => 'Ошибка загрузки файла: '.$handle->error
				]);
				return true;
			}
		}
		\Apps::response()->setData([
			'res' => false,
			'message' => 'Ошибка загрузки файла'
		]);
		return true;
	}

	public function pasteAction() {
		$post = \Apps::request()->post();
		if (!isset($post['pathTo']) || !isset($post['pathFrom'])) {
			\Apps::response()->setData([
				'res' => false,
				'message' => 'Отсутствует переменая path'
			]);
		}

		$fullPathTo = realpath(ROOT.$post['pathTo']);
		$pathFrom = explode(',', $post['pathFrom']);
		foreach ($pathFrom as $key => $path) {
			$path = ROOT.'/'.$path;
			$onePath = $fullPathTo.'/'.basename($path);
			if (!is_dir($onePath)) {
				mkdir($onePath, 0700, true);
			}

			if ($post['type'] === 'copy') {
				$this->copy($path, $onePath);
			} else if ($post['type'] === 'cut') {
				$this->copy($path, $onePath);
				$this->unlinkDir($path);
			}
		}

		$folders = [];
		$files = [];

		$res = $this->openFolderList($folders, $files, $post['pathTo']);
		if ($res === false) {
			return true;
		}

		\Apps::response()->setData([
			'res' => true,
			'folders' => $folders,
			'files' => $files,
			'path' => $post['pathTo'],
			'message' => 'Успешно '.($post['type'] === 'copy' ? 'скопировано' : 'перемещено')
		]);
		return true;
	}
}
