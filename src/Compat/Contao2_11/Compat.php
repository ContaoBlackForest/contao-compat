<?php

namespace Compat\Contao2_11;

class Compat implements \Compat\Compat
{
	static public function resolveFile($file, $fallback = null)
	{
		if ($fallback !== null && !file_exists(TL_ROOT . '/' . $file)) {
			return $fallback;
		}

		return $file;
	}
}