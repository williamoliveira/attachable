<?php
namespace Williamoliveira\Attachable\Services;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class RemoteFileManager
 * @package Williamoliveira\Attachable\Services
 */
class RemoteFileManager
{

    public $tempDir;

    /**
     * RemoteFileManager constructor.
     * @param null $tempDir
     */
    function __construct($tempDir = null)
    {
        $this->setTempDir($tempDir);
    }

    /**
     * @return bool
     */
    private function prepareDirectory()
    {
        if(!is_dir($this->tempDir)){
            return mkdir($this->tempDir, 0775, true);
        }
        return true;
    }

    /**
     * @param $dir
     */
    public function setTempDir($dir)
    {
        $this->tempDir = !empty($dir) ? $dir : storage_path('attachable/tmp/');
    }

    /**
     * @param $url
     * @return UploadedFile
     */
    public function get($url)
    {
        $this->prepareDirectory();
        $fileAttributes = pathinfo($url);
        $filename = $this->tempDir . $fileAttributes['basename'];

        file_put_contents($filename, fopen($url, 'r'));

        $this->registerDeleteFileEvent($filename);

        $mimetypeGuesses = MimeTypeGuesser::getInstance();
        $mimetype = $mimetypeGuesses->guess($filename);

        return new UploadedFile($filename, $fileAttributes['basename'], $mimetype);

    }

    /**
     * @param $filename
     */
    private function registerDeleteFileEvent($filename)
    {
        app()->terminating(function () use ($filename){
            if(file_exists($filename)){
                unlink($filename);
            }
        });
    }

}