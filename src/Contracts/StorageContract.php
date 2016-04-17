<?php namespace Williamoliveira\Attachable\Contracts;

/**
 * Interface StorageContract
 * @package Williamoliveira\Attachable\Contracts
 */
interface StorageContract {

    /**
     * @param $disk
     * @return mixed
     */
    public function setDisk($disk);

    /**
     * @param $filepath
     * @return mixed
     */
    public function getUrl($filepath);

    /**
     * @param $from
     * @param $to
     * @return mixed
     */
    public function move($from, $to);

    /**
     * @param $path
     * @param $contents
     * @return mixed
     */
    public function put($path, $contents);

    /**
     * @param $paths
     * @return mixed
     */
    public function delete($paths);

    /**
     * @param $dir
     * @return mixed
     */
    public function deleteDirectory($dir);

}