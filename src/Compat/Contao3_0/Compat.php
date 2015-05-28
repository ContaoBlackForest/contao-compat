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

namespace Compat\Contao3_0;

use Controller;
use Database;
use FilesModel;
use Model\Collection;

/**
 * Class Compat
 *
 * @package Compat\Contao3_0
 */
class Compat extends Controller implements \Compat\Compat
{
    /**
     * @var Compat
     */
    static protected $instance = null;

    /**
     * @return Compat
     */
    protected static function getInstance()
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
    public static function getFileModel($file)
    {

        if (empty($file)) {
        /**
         * No file model source given
         */
            return false;

        } elseif ($file instanceof Collection) {
        /**
         * Get file from a collection
         */
            $file = $file->current();

        } elseif (is_numeric($file)) {
        /**
         * Get file by DBAFS ID
         */
            $file = FilesModel::findByPk($file, array('uncached' => true));

        } elseif (is_string($file)) {
        /**
         * Get file by pathname
         */

            // normalize path
            $file = preg_replace('~//+~', '/', $file);

            // fallback to virtual files model if
            // ... file not in upload path
            // ... dbafs is disabled
            // ... file does not exists
            if (!preg_match('#^' . preg_quote($GLOBALS['TL_CONFIG']['uploadPath']) . '/#', $file) ||
                !$GLOBALS['TL_DCA']['tl_files']['config']['databaseAssisted']
            ) {
                $model = new FilesModel();
                $model->path = $file;
                $file = $model;
            } else {
                $file = FilesModel::findByPath($file, array('uncached' => true));
            }
        } else {
            throw new \RuntimeException(
                'Illegal argument of type ' . gettype($file) . ' given to Compat::resolveFile()'
            );
        }

        /**
         * Get path from model
         */
        if ($file instanceof FilesModel) {
            return $file;
        } elseif ($file instanceof Collection && $file->current() !== null) {
            return $file->current();
        }

        return false;
    }

    /**
     * @param \Compat\FilesModel|int|string $file
     * @param null                          $fallback
     * @return bool|\Compat\FilesModel|FilesModel|int|mixed|null|string
     */
    public static function resolveFile($file, $fallback = null)
    {
        if (empty($file)) {
            return $fallback;
        }

        // the ctype_print check is for forward compatibility with binary UUIDs
        if (ctype_print($file) && file_exists(TL_ROOT . '/' . $file)) {
            return $file;
        }

        $file = static::getFileModel($file);

        if (!$file || $fallback !== null && !file_exists(TL_ROOT . '/' . $file->path)) {
            return $fallback;
        }

        return $file->path;
    }

    /**
     * @param $file
     * @return FilesModel|int|mixed
     */
    public static function syncFileModel($file)
    {
        // normalize path
        $file = preg_replace('~//+~', '/', $file);

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
            $hash = '';
        } else {
            $type = 'file';
            $hash = md5_file(TL_ROOT . '/' . $file);

            if (preg_match('#\.([^\.]+)$#', $file, $match)) {
                $extension = $match[1];
            }
        }

        /** @var FilesModel $fileModel */
        $fileModel = FilesModel::findByPath($file, array('uncached' => true));

        if ($fileModel) {
            $fileModel->pid = $pid;
            $fileModel->tstamp = time();
            $fileModel->type = $type;
            $fileModel->hash = $hash;
            $fileModel->save();
        } else {
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

        return $fileModel;
    }

    /**
     * @param string $file
     * @return FilesModel|int|mixed|null
     */
    public static function syncFile($file)
    {
        $fileModel = static::syncFileModel($file);

        if ($fileModel instanceof \FilesModel) {
            return $fileModel->id;
        }

        return $fileModel;
    }

    /**
     * @param \Compat\FilesModel|int|string $file
     * @return bool
     */
    public static function deleteFile($file)
    {
        $file = static::getFileModel($file);

        if ($file) {
            $result = true;
            if (file_exists(TL_ROOT . '/' . $file->path)) {
                $result = \Files::getInstance()->delete($file->path);
            }
            $file->delete();

            return $result;
        }

        return false;
    }
}
