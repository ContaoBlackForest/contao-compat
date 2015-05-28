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

namespace Compat;

/**
 * Interface Compat
 *
 * @package Compat
 */
interface Compat
{
    /**
     * Resolve a file source and return the pathname.
     *
     * @param string|int|FilesModel $file
     *     The file source.
     * @param string                $fallback
     *     If not *null* Test if the file really exists in the filesystem and if not, return $fallback.
     *     $fallback = false means, check for existence and return *false* if file not exists.
     *     IF $file is a DBAFS ID and the record is not found, $fallback is also returned.
     *
     * @return string|null The pathname to the file or $fallback if file not found.
     */
    public static function resolveFile($file, $fallback = null);

    /**
     * Store a file pathname in tl_files and return the file ID.
     *
     * @param string $file
     *     The file pathname.
     *
     * @return string|int The pathname if dbafs is not supported or disabled or the tl_files record ID.
     */
    public static function syncFile($file);

    /**
     * Delete a file source.
     *
     * @param string|int|FilesModel $file
     *     The file source.
     *
     * @return bool Return <em>true</em> if the file was deleted.
     */
    public static function deleteFile($file);
}
