<?php

if (version_compare(VERSION, '3', '>=')) {
	class_exists('Compat\Contao3_0\Compat');
	class_alias('Compat\Contao3_0\Compat', 'Compat');
}
else {
	class_exists('Compat\Contao2_11\Compat');
	class_alias('Compat\Contao2_11\Compat', 'Compat');
}