<?php
namespace modules\files\models;

class Main
{
	protected function copy($d1, $d2) {
		if (is_dir($d1)) {
			if (!is_dir($d2)) {
				if (!mkdir($d2, 0700, true)) 
					return false;
			}

			$d = dir($d1);
			while (false !== ($entry = $d->read())) {
				if ($entry !== '.' && $entry !== '..') {
					$this->copy("$d1/$entry", "$d2/$entry");
				}
			}
			$d->close();
		} else {
			if (!copy($d1, $d2)) {
				return false;
			}
		}
	}

	protected function fileType($fullPath) {
		$type = mime_content_type($fullPath)?? 'none';
		$types = explode('/', $type);
		return isset($types[0]) ? $types[0] : 'none';
	}

	protected function openFolderList(array &$folders, array &$files, string $path): bool {
		if ($path === '/') {
			\Apps::response()->setData([
				'res' => false,
				'message' => 'Отказано в доступе'
			]);
			return false;
		}

		if (!is_dir(ROOT.$path)) {
			\Apps::response()->setData([
				'res' => false,
				'message' => 'Папка отсутствует'
			]);
			return false;
		}
		$prefix_path = trim($path, '/');
		foreach (scandir(ROOT.$path) as $path) {
			if ($path === '..' || $path === '.' || $path === '.htaccess') {
				continue;
			}

			$path = '/'.$prefix_path.'/'.$path;
			$fullPath = realpath(ROOT.$path);
			$pathinfo = pathinfo($fullPath);

			if (is_dir($fullPath)) {
				$folders[] = [
					'name' => $pathinfo['basename'],
					'path' => str_replace(ROOT, '', $fullPath),
					'hash' => md5($fullPath.microtime(true).time()),
					'type' => 'folder'
				];
			}

			if (is_file($fullPath)) {
				$files[] = [
					'name' => $pathinfo['basename'],
					'path' => str_replace(ROOT, '', $fullPath),
					'hash' => md5($fullPath.microtime(true).time()),
					'type' => $this->fileType($fullPath)
				];
			}
		}
		return true;
	}

	protected function unlinkDir($path) {
		if (is_dir($path)) {
			$files = array_diff(scandir($path), ['.','..']);
			foreach ($files as $file) {
				(is_dir($path.'/'.$file)) ? $this->unlinkDir($path.'/'.$file) : unlink($path.'/'.$file);
			}
			return rmdir($path);
		} else {
			return unlink($path);
		}
	}

	protected function updateImage(&$handle, $path): void {
		foreach ($this->settings['images']['thum_list'] as $item) {
			$handle->image_resize = $item['resize'];
			$handle->image_ratio = $item['ratio'];
			$handle->image_ratio_crop = $item['ratio_crop'];
			$handle->image_y = $item['size'];
			$handle->image_x = $item['size'];

			if (!is_dir($path.'/'.$item['name'])) {
				@mkdir($path.'/'.$item['name'], 0700, true);
			}

			$handle->process($path.'/'.$item['name']);
		}
	}
}
