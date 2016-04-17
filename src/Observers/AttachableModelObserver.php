<?php
namespace Williamoliveira\Attachable\Observers;

use Williamoliveira\Attachable\Contracts\Processors\FileProcessorContract;
use Williamoliveira\Attachable\Contracts\Processors\InterventionImageProcessorContract;
use Williamoliveira\Attachable\Contracts\Processors\ProcessorContract;
use Williamoliveira\Attachable\Models\AttachableModel;

/**
 * Class AttachableModelObserver
 * @package Williamoliveira\Attachable\Observers
 */
class AttachableModelObserver
{
    protected $imageProcessor;
    protected $fileProcessor;

    /**
     * AttachableModelObserver constructor.
     * @param InterventionImageProcessorContract $imageProcessor
     * @param FileProcessorContract $fileProcessor
     */
    public function __construct(
        InterventionImageProcessorContract $imageProcessor,
        FileProcessorContract $fileProcessor
    )
    {
        $this->imageProcessor = $imageProcessor;
        $this->fileProcessor = $fileProcessor;
    }


    /**
     * @param AttachableModel $model
     * @return mixed
     */
    public function created(AttachableModel $model)
    {
        return $this->getProcessor($model)->store($model);
    }

    /**
     * @param AttachableModel $model
     * @return mixed
     */
    public function updated(AttachableModel $model)
    {
        return $this->getProcessor($model)->update($model);
    }

    /**
     * @param AttachableModel $model
     * @return mixed
     */
    public function deleted(AttachableModel $model)
    {
        return $this->getProcessor($model)->delete($model);
    }

    /**
     * @param AttachableModel $model
     * @return ProcessorContract
     */
    protected function getProcessor(AttachableModel $model)
    {
        return $model->useInterventionImage() ? $this->imageProcessor : $this->fileProcessor;
    }

}