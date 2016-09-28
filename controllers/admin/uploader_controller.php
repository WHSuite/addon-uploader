<?php

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Addon\Uploader\Libraries\Get as UploaderGet;
use Addon\Uploader\Libraries\Process as UploaderProcess;

class UploaderController extends \AppController
{
    /**
     * process and download a file given the details
     *
     * @param string    The model
     * @param int       Model ID
     * @param string    the filename to download
     * @return file output
     */
    public function download($model_name, $model_id, $filename)
    {
        $file = UploaderGet::file($model_name, $model_id, $filename);

        $finfo = new finfo(FILEINFO_MIME);
        $filepath = $file->getRealPath();
        $content = file_get_contents($filepath);
        $filename = UploaderProcess::filename($file->getFilename());

        $Http = new \Whsuite\Http\Http;
        $Response = $Http->newResponse();

        $Response->setHeaders(
            array(
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Type' => $finfo->file($filepath),
                'Content-Length' => strlen($content),
                'Content-Disposition' => 'attachment; filename=' . $filename
            )
        );
        $Response->setContent($content);

        $Http->send($Response);
    }


    /**
     * ajax delete function for deleting files via the admin form.
     *
     * @param string    The model
     * @param int       Model ID
     * @param string    the filename to download
     * @return bool
     */
    public function ajaxDelete($model_name, $model_id, $filename)
    {
        $file = UploaderGet::file($model_name, $model_id, $filename);

        $filesystem = new Filesystem();

        try {

            $filesystem->remove(
                $file->getRealPath()
            );
        } catch(IOException $e) {

            return false;
        }

        return true;
    }

}
