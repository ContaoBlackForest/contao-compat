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

namespace Compat\Contao2_11;

/**
 * Class Compat
 *
 * @package Compat\Contao2_11
 */
class Compat implements \Compat\Compat
{
    /**
     * @param \Compat\FilesModel|int|string $file
     * @param null                          $fallback
     * @return \Compat\FilesModel|int|mixed|null|string
     */
    public static function resolveFile($file, $fallback = null)
    {
        // normalize path
        $file = preg_replace('~//+~', '/', $file);

        if ($fallback !== null && !file_exists(TL_ROOT . '/' . $file)) {
            return $fallback;
        }

        return $file;
    }

    /**
     * @param string $file
     * @return mixed|string
     */
    public static function syncFile($file)
    {
        // normalize path
        $file = preg_replace('~//+~', '/', $file);

        return $file;
    }

    /**
     * @param \Compat\FilesModel|int|string $file
     * @return bool
     */
    public static function deleteFile($file)
    {
        // normalize path
        $file = preg_replace('~//+~', '/', $file);

        if (file_exists(TL_ROOT . '/' . $file)) {
            return \Files::getInstance()->delete($file);
        }

        return false;
    }
}
