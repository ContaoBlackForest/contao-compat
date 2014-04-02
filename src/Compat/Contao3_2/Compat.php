<?php

namespace Compat\Contao3_2;

use Controller;
use Database;
use FilesModel;
use Model\Collection;
use Compat\Contao3_0\Compat as Compat3_0;

class Compat extends Compat3_0
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
		 * Get file by DBAFS UUID
		 */
		if (\Validator::isUuid($file)) {
			$file = FilesModel::findByUuid($file, array('uncached' => true));
		}

		/**
		 * Search the old way
		 */
		else {
			return parent::getFileModel($file);
		}

		/**
		 * Get path from model
		 */
		if ($file instanceof FilesModel) {
			return $file;
		}

		return false;
	}

	static public function syncFile($file)
	{
		$fileModel = static::syncFileModel($file);

		if (empty($fileModel->uuid)) {
			$fileModel->uuid = \Database::getInstance()->getUuid();
			$fileModel->save();
		}

		return $fileModel->uuid;
	}
}