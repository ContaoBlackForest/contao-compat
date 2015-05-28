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

namespace Compat\Contao3_2;

use Compat\Contao3_0\Compat as Compat3_0;
use Database;
use FilesModel;

/**
 * Class Compat
 *
 * @package Compat\Contao3_2
 */
class Compat extends Compat3_0
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

        } elseif (\Validator::isUuid($file)) {

            /**
             * Get file by DBAFS UUID
             */
            $file = FilesModel::findByUuid($file, array('uncached' => true));

        } else {

            /**
             * Search the old way
             */
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

    /**
     * @param string $file
     * @return mixed|null|string
     */
    public static function syncFile($file)
    {
        $fileModel = static::syncFileModel($file);

        if ($fileModel instanceof \FilesModel && empty($fileModel->uuid)) {
            $fileModel->uuid = \Database::getInstance()->getUuid();
            $fileModel->save();
        }

        return $fileModel->uuid;
    }
}
