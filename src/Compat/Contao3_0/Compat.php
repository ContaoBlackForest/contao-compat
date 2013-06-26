<?php

namespace Compat\Contao3_0;

use Controller;
use Database;
use FilesModel;
use Model\Collection;

class Compat extends Controller implements \Compat\Compat
{
	/**
	 * @var Compat
	 */
	static protected $instance = null;

	/**
	 * @return Compat
	 */
	static protected function getInstance()
	{
		if (static::$instance === null) {
			static::$instance = new Compat();
		}
		return static::$instance;
	}

	/**
	 * @param $file
	 *
	 * @return bool|FilesModel
	 * @throws \RuntimeException
	 */
	static public function getFileModel($file)
	{
		/**
		 * Get file from a collection
		 */
		if ($file instanceof Collection) {
			$file = $file->current();
		}

		/**
		 * Get file by DBAFS ID
		 */
		else if (is_numeric($file)) {
			$file = FilesModel::findByPk($file);
		}

		/**
		 * Get file by pathname
		 */
		else if (is_string($file)) {
			$file = FilesModel::findByPath($file);
		}

		else {
			throw new \RuntimeException('Illegal argument of type ' . gettype($file) . ' given to Compat::resolveFile()');
		}

		/**
		 * Get path from model
		 */
		if ($file instanceof FilesModel) {
			return $file;
		}

		return false;
	}

	static public function resolveFile($file, $fallback = null)
	{
		$file = static::getFileModel($file);

		if (!$file || $fallback !== null && !file_exists(TL_ROOT . '/' . $file->path)) {
			return $fallback;
		}

		return $file->path;
	}

	static public function syncFile($file)
	{
		static::getInstance()->loadDataContainer('tl_files');

		// break if
		// ... file not in upload path
		// ... dbafs is disabled
		// ... file does not exists
		if (!preg_match('#^' . preg_quote($GLOBALS['TL_CONFIG']['uploadPath']) . '/#', $file) ||
			!$GLOBALS['TL_DCA']['tl_files']['config']['databaseAssisted'] ||
			!file_exists(TL_ROOT . '/' . $file)
		) {
			return $file;
		}

		// break recursive call
		if ($file == $GLOBALS['TL_CONFIG']['uploadPath']) {
			return 0;
		}

		// get parent id
		$pid = static::syncFile(dirname($file));
		$extension = '';
		$name = basename($file);

		if (is_dir(TL_ROOT . '/' . $file)) {
			$type = 'folder';
			$hash = '';
		}
		else {
			$type = 'file';
			$hash = md5_file(TL_ROOT . '/' . $file);

			if (preg_match('#\.([^\.]+)$#', $file, $match)) {
				$extension = $match[1];
			}
		}

		/** @var FilesModel $fileModel */
		$fileModel = FilesModel::findByPath($file, array('uncached' => true));

		if ($fileModel) {
			$fileModel->pid = $pid;
			$fileModel->tstamp = time();
			$fileModel->type = $type;
			$fileModel->hash = $hash;
			$fileModel->save();
		}
		else {
			$fileModel = new FilesModel();
			$fileModel->pid = $pid;
			$fileModel->tstamp = time();
			$fileModel->type = $type;
			$fileModel->path = $file;
			$fileModel->extension = $extension;
			$fileModel->hash = $hash;
			$fileModel->name = $name;
			$fileModel->save();
		}

		return $fileModel->id;
	}

	static public function deleteFile($file)
	{
		$file = static::getFileModel($file);

		if ($file) {
			$result = true;
			if (file_exists(TL_ROOT . '/' . $file->path)) {
				$result = \Files::getInstance()->delete($file->path);
			}
			$file->delete();
			return $result;
		}

		return false;
	}
}