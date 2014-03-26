<?php

namespace Compat\Contao2_11;

class Compat implements \Compat\Compat
{
	static public function resolveFile($file, $fallback = null)
	{
		// normalize path
		$file = preg_replace('~//+~', '/', $file);

		if ($fallback !== null && !file_exists(TL_ROOT . '/' . $file)) {
			return $fallback;
		}

		return $file;
	}

	static public function syncFile($file)
	{
		// normalize path
		$file = preg_replace('~//+~', '/', $file);

		return $file;
	}

	static public function deleteFile($file)
	{
		// normalize path
		$file = preg_replace('~//+~', '/', $file);

		if (file_exists(TL_ROOT . '/' . $file)) {
			return \Files::getInstance()->delete($file);
		}

		return false;
	}
}