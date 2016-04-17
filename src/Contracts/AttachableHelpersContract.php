<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 07/04/16
 * Time: 21:40
 */

namespace Williamoliveira\Attachable\Contracts;


use Symfony\Component\HttpFoundation\File\File;

interface AttachableHelpersContract
{
    /**
     * @param File $file
     * @return bool
     */
    public function isImage(File $file);


    /**
     * Sanitize a Filename (without extension)
     *
     * @param $filename
     * @return string
     */
    public function sanitizeFilename($filename);
}