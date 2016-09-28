<?php
namespace Addon\Uploader\Libraries;

use Symfony\Component\Finder\Finder;

class Get
{

    /**
     * get all the files from a directory given the model name / id
     *
     * @param string    The model name the files belong to
     * @param int       The model id the files belong to
     * @return array    Array of all the files
     */
    public static function allFiles($model_name, $model_id)
    {
        $finder = new Finder();

        $dir = STORAGE_DIR . DS . 'uploader' . DS . $model_name . DS . $model_id;

        if (file_exists($dir) && is_dir($dir)) {

            return $finder->files()->in($dir);
        } else {

            return false;
        }
    }

    /**
     * get a specific file given model name / id and filename
     *
     * @param string    The model name the files belong to
     * @param int       The model id the files belong to
     * @param string    the filename to download
     * @return array    Array of all the files
     */
    public static function file($model_name, $model_id, $filename)
    {
        $finder = new Finder();

        $dir = STORAGE_DIR . DS . 'uploader' . DS . $model_name . DS . $model_id;

        if (file_exists($dir . DS . $filename) && is_file($dir . DS . $filename)) {

            $files = $finder->files()->in($dir)->name($filename);
            foreach ($files as $file) {

                return $file;
            }

        } else {

            return false;
        }
    }

}
