<?php

namespace Compat\Contao2_11;

use FilesModel;
use Model\Collection;

class Compat implements \Compat\Compat
{
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
}