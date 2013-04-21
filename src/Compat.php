<?php

/**
 * Class Compat
 */
if (version_compare(VERSION, '3', '>=')) {
	class Compat extends \Compat\Contao3_0\Compat
	{
	}
}
else {
	class Compat extends \Compat\Contao2_11\Compat
	{
	}
}
