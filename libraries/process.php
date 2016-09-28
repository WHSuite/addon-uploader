<?php
namespace Addon\Uploader\Libraries;

use \Whsuite\Inputs\Files as FilesInput;
use Symfony\Component\Filesystem\Filesystem;
use Monolog\Logger;

class Process
{
    /**
     * call this function in the after save callback of the controller to process the uploads
     *
     * @param string    the model name the files want to belong to.
     * @param object    the model we have just saved
     * @return array    array of paths to new file
     */
    public static function uploads($model_name, $main_model)
    {
        $uploads = FilesInput::get('data');

        // nothing to process return
        if (empty($uploads)) {

            return;
        }

        // check if the arrays have been sorted into nicer format yet
        if (! isset($uploads['arrays_sorted'])) {

            $uploads = self::sortArray($uploads);
            $uploads['arrays_sorted'] = true;
            FilesInput::set('data', $uploads);
        }

        // get the primary key so we can arrange by folders
        $pk = $main_model->getKeyname();
        $file_path = STORAGE_DIR . DS . 'uploader' . DS . $model_name . DS . $main_model->{$pk} . DS;

        $filesystem = new Filesystem();
        $time = time();
        $uploaded = array();

        // loop files and upload
        foreach ($uploads[$model_name] as $key => $file) {

            preg_match_all("([a-zA-Z0-9._-]+)", $file['name'], $matches);

            if (isset($matches['0'])) {

                $filename = implode('', $matches['0']);
            } else {

                $filename = $file['name'];
            }

            $new_file = $file_path . $time . '__' . $filename;

            try {

                $filesystem->copy($file['tmp_name'], $new_file, true);
                $uploaded[] = $new_file;

            } catch(Exception $e) { }
        }

        return $uploaded;
    }

    /**
     * delete a file (if given) or delete a whole directory of files
     *
     * @param string    Model name to delete
     * @param int       Model id to delete (optional)
     * @param string    Specific file to delete (optional)
     */
    public static function delete($model_name, $model_id = null, $filename = null)
    {
        $dir = STORAGE_DIR . DS . 'uploader' . DS . $model_name;

        // if we are deleting a specific id add it to the directory
        if (! empty($model_id)) {

            $dir .= DS . $model_id;
        }

        // if we are deleting a specific file, also check they haven't passed model_id as null
        if (! empty($model_id) && ! empty($filename)) {

            $dir .= DS . $filename;
        }

        $filesystem = new Filesystem();

        try {

            $filesystem->remove($dir);

        } catch(IOException $e) {

            $message = 'There was an issue deleting some files: ' . $model_name . '/';
            $message .= (! empty($model_id)) ? $model_id : 'no_model_id';
            $message .= '/';
            $message .= (! empty($filename)) ? $filename : 'no_filename';

            \App::get('logger')->addRecord(Logger::NOTICE, $message, debug_backtrace());
        }
    }

    /**
     * sort the file array into a nicer format to work with
     *
     * @param array     array from the file input
     * @return array    the sorted array
     */
    public static function sortArray($uploads)
    {
        $return = array();

        if (isset($uploads['name'])) {

            foreach ($uploads['name'] as $model => $items) {

                $return[$model] = array();

                foreach ($items as $key => $file) {

                    $item = array(
                        'name' => $file['filename'],
                        'type' => $uploads['type'][$model][$key]['filename'],
                        'tmp_name' => $uploads['tmp_name'][$model][$key]['filename'],
                        'error' => $uploads['error'][$model][$key]['filename'],
                        'size' => $uploads['size'][$model][$key]['filename']
                    );

                    $return[$model][$key] = $item;
                }
            }
        }

        return $return;
    }

    /**
     * process the filename to remove the timestamp prefix
     *
     * @param string    Filename to process
     * @return string   Filename with timestamp removed
     */
    public static function filename($filename)
    {
        $filename = explode('__', $filename, 2);
        return $filename['1'];
    }
}
