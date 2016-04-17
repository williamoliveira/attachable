<?php
namespace Williamoliveira\Attachable\Services;

use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;
use Williamoliveira\Attachable\Contracts\AttachableHelpersContract;

/**
 * Class AttachableHelpers
 * @package Williamoliveira\Attachable\Services
 */
class AttachableHelpers implements AttachableHelpersContract
{

    /**
     * @param File $file
     * @return bool
     */
    public function isImage(File $file)
    {
        $imageMimes = [
            'bmp' => 'image/bmp',
            'gif' => 'image/gif',
            'jpeg' => ['image/jpeg', 'image/pjpeg'],
            'jpg' => ['image/jpeg', 'image/pjpeg'],
            'jpe' => ['image/jpeg', 'image/pjpeg'],
            'png' => 'image/png',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
        ];

        $mime = $file->getMimeType();

        foreach($imageMimes as $imageMime) {
            if(in_array($mime, (array)$imageMime)){
                return true;
            }
        }
        return false;
    }


    /**
     * Sanitize a Filename (without extension)
     *
     * @param $filename
     * @return mixed|string
     */
    public function sanitizeFilename($filename)
    {
        return Str::slug($filename);
        
//        $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
//
//        $filename = preg_replace("#\x{00a0}#siu", ' ', $filename);
//        $filename = str_replace($special_chars, '', $filename);
//        $filename = str_replace(array('%20', '+'), '-', $filename);
//        $filename = preg_replace('/[\r\n\t -]+/', '-', $filename);
//        $filename = trim($filename, '.-_');
//
//        return $filename;
    }

}