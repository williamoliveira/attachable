<?php
namespace Williamoliveira\Attachable\Processors;

use Williamoliveira\Attachable\Contracts\Processors\FileProcessorContract;
use Williamoliveira\Attachable\Contracts\StorageContract;
use Williamoliveira\Attachable\Models\AttachableModel;

/**
 * Class FileProcessor
 * @package Williamoliveira\Attachable\Processors
 */
class FileProcessor implements FileProcessorContract
{

    protected $storage;
    
    /**
     * FileProcessor constructor.
     * @param StorageContract $storage
     */
    function __construct(StorageContract $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param AttachableModel $model
     * @return mixed
     */
    public function store(AttachableModel $model)
    {
        $this->setDisk($model);

        $fileContents = file_get_contents($model->getUploadedFile()->getRealPath());

        return $this->storage->put(
            $model->getPath(),
            $fileContents
        );
    }

    /**
     * @param AttachableModel $model
     * @return mixed|void
     */
    public function update(AttachableModel $model)
    {
//        $this->delete($model);
        
        return $this->store($model);
    }

    /**
     * @param AttachableModel $model
     * @return mixed|void
     */
    public function delete(AttachableModel $model)
    {
        $this->setDisk($model);
        
        return $this->storage->delete($model->getPath());
    }

    /**
     * @param AttachableModel $model
     */
    protected function setDisk(AttachableModel $model)
    {
        $this->storage->setDisk($model->disk);
    }

}