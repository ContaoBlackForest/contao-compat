<?php

/**
 * Compatibility layer to the Contao API
 * Copyright (C) 2015 ContaoBlackForest
 *
 * PHP version 5
 *
 * @copyright   bit3 UG 2013
 * @author      Tristan Lins <tristan.lins@bit3.de>
 * @author      Dominik Tomasi <dominik.tomasi@gmail.com>
 * @author      Sven Baumann <baumannsv@gmail.com>
 * @package     doctrine-orm
 * @license     LGPL
 * @filesource
 */

// @codingStandardsIgnoreStart
if (version_compare(VERSION, '3.2', '>=')) {
    class Compat extends \Compat\Contao3_2\Compat
    {
    }
} elseif (version_compare(VERSION, '3', '>=')) {
    class Compat extends \Compat\Contao3_0\Compat
    {
    }
} else {
    class Compat extends \Compat\Contao2_11\Compat
    {
    }
}
// @codingStandardsIgnoreEnd
