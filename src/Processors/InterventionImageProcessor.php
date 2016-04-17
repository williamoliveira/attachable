<?php
namespace Williamoliveira\Attachable\Processors;

use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Williamoliveira\Attachable\Contracts\Processors\FileProcessorContract;
use Williamoliveira\Attachable\Contracts\Processors\InterventionImageProcessorContract;
use Williamoliveira\Attachable\Contracts\StorageContract;
use Williamoliveira\Attachable\Exceptions\OnlyImagesException;
use Williamoliveira\Attachable\Models\AttachableModel;

/**
 * Class InterventionImageProcessor
 * @package Williamoliveira\Attachable\Processors
 */
class InterventionImageProcessor extends FileProcessor implements InterventionImageProcessorContract
{

    /**
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * InterventionImageProcessor constructor.
     * 
     * @param ImageManager $imageManager
     * @param StorageContract $storage
     */
    function __construct(StorageContract $storage, ImageManager $imageManager)
    {
        parent::__construct($storage);
        
        $this->imageManager = $imageManager;
    }

    /**
     * @param AttachableModel $model
     * @return boolean
     * @throws OnlyImagesException
     */
    public function store(AttachableModel $model)
    {
        $this->setDisk($model);

        try {
            $image = $this->imageManager->make($model->getUploadedFile()->getFileInfo());

            $this->processTemplates($model, $image);
        }
        catch(NotReadableException $e) {
            //Intervention Image says its not an image, ok, lets process as a File so

            //wait, unless $only_images propriety is true
            if($model->onlyImages){
                throw new OnlyImagesException();
            }

            return app(FileProcessorContract::class)->store($model);
        }

        return true;
    }


    /**
     * @param AttachableModel $model
     * @param Image $image
     */
    protected function processTemplates(AttachableModel $model, Image $image)
    {
        $templates = $model->imageTemplates();

        if(empty($templates)){
            $this->saveSingleTemplate($model, $image, 'original');
        }

        foreach($templates as $templateName => $closure) {

            $image_clone = clone $image;
            $imageProcessed = $closure($image_clone);

            $this->saveSingleTemplate($model, $imageProcessed, $templateName);
        }

    }

    /**
     * @param AttachableModel $model
     * @param Image $image
     * @param $templateName
     */
    protected function saveSingleTemplate(AttachableModel $model, Image $image, $templateName)
    {
        $this->storage->put(
            $model->getPath($templateName),
            $image->encode($model->file_extension)
        );
    }

    /**
     * @param AttachableModel $model
     * @return mixed
     */
    public function delete(AttachableModel $model)
    {
        $this->setDisk($model);
        
        $templates = $model->imageTemplates();

        foreach($templates as $templateName => $closure) {
            $this->storage->delete($model->getPath($templateName));
        }
    }
}