<?php
namespace Williamoliveira\Attachable\Storage;

use Illuminate\Contracts\Filesystem\Factory as Storage;
use Williamoliveira\Attachable\Contracts\StorageContract;

/**
 * Class LaravelFilesystem
 * @package Williamoliveira\Attachable\Storage
 */
class LaravelFilesystem implements StorageContract
{

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $storage;

    protected $storageFactory;

    protected $diskName;

    /**
     * LaravelFilesystem constructor.
     * @param Storage $storageFactory
     */
    function __construct(Storage $storageFactory)
    {
        $this->storageFactory = $storageFactory;

        $this->setDisk(config('attachable.default_disk'));
    }

    /**
     * @param $disk
     * @return $this
     */
    public function setDisk($disk)
    {
        $this->storage = $this->storageFactory->disk($disk);
        $this->diskName = $disk;

        return $this;
    }

    /**
     * @param $filepath
     * @return mixed|void
     */
    public function getUrl($filepath)
    {
        switch($this->diskName) {

            case 's3':
                return $this->storage->getDriver()
                    ->getAdapter()
                    ->getClient()
                    ->getObjectUrl();

            case 'local_public':
                $storagepath = str_ireplace(
                        public_path(), '',
                        config('filesystems.disks.local_public.root')
                    );

                return url($storagepath . '/' . $filepath);

        }

        return null;
    }

    /**
     * @param $path
     * @return string
     */
    public function get($path)
    {
        return $this->storage->get($path);
    }

    /**
     * @param $from
     * @param $to
     * @return bool|mixed
     */
    public function move($from, $to)
    {
        return $this->storage->move($from, $to);
    }

    /**
     * @param $path
     * @param $contents
     * @return bool|mixed
     */
    public function put($path, $contents)
    {
        return $this->storage->put($path, $contents);
    }

    /**
     * @param $paths
     * @return bool|mixed
     */
    public function delete($paths)
    {
        return $this->storage->delete($paths);
    }

    /**
     * @param $dir
     * @return bool|mixed
     */
    public function deleteDirectory($dir)
    {
        return $this->storage->deleteDirectory($dir);
    }
}