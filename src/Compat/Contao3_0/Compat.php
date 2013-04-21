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

	static public function resolveFile($file, $fallback = null)
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

			/**
			 * File not found -> return $fallback
			 */
			if (!$file) {
				return $fallback;
			}
		}

		/**
		 * Get path from model
		 */
		if ($file instanceof FilesModel) {
			$file = $file->path;
		}
		else if (!is_string($file)) {
			throw new \RuntimeException('Illegal argument of type ' . gettype($file) . ' given to Compat::resolveFile()');
		}

		if ($fallback !== null && !file_exists(TL_ROOT . '/' . $file)) {
			return $fallback;
		}

		return $file;
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
		}
		else {
			$type = 'file';
			$hash = md5_file(TL_ROOT . '/' . $file);

			if (preg_match('#\.([^\.]+)$#', $file, $match)) {
				$extension = $match[1];
			}
		}

		/** @var FilesModel $fileModel */
		$fileModel = FilesModel::findByPath($file);

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
}