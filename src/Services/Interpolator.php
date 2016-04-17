<?php
namespace Williamoliveira\Attachable\Services;


use Williamoliveira\Attachable\Contracts\InterpolatorContract;
use Williamoliveira\Attachable\Models\AttachableModel;

/**
 * Class Interpolator
 * @package Williamoliveira\Attachable\Services
 */
class Interpolator implements InterpolatorContract{

    private $startSymbol = "{";
    
    private $endSymbol = "}";

    private $fileInterpolations = [
        'id',
        'filename',
        'fileroot',
        'extension'
    ];

    private $imageInterpolations = [
        'id',
        'filename',
        'fileroot',
        'extension',
        'template'
    ];

    /**
     * @param AttachableModel $model
     * @param string $templateName
     * @return string
     */
    public function parsePath(AttachableModel $model, $templateName = 'original')
    {
        if($model->use_intervention_image){
            return $this->parseImagePath($model, $templateName);
        }

        return $this->parseFilePath($model);
    }

    /**
     * @param AttachableModel $model
     * @return string
     */
    public function parseFilePath(AttachableModel $model)
    {
        $id = $model->id;
        $filename = $model->file_name;
        $fileroot = $model->file_root;
        $extension = $model->file_extension;

        $path = $model->getUnparsedFilesPath();

        foreach($this->fileInterpolations as $interpolation) {
            $path = str_ireplace(
                $this->startSymbol . $interpolation . $this->endSymbol,
                $$interpolation,
                $path
            );
        }

        return $path;
    }

    /**
     * @param AttachableModel $model
     * @param string $template
     * @return string
     */
    public function parseImagePath(AttachableModel $model, $template = 'original')
    {
        $id = $model->id;
        $filename = $model->file_name;
        $extension = $model->file_extension;
        $fileroot = $model->file_root;

        $path = $model->getUnparsedImagesPath();

        foreach($this->imageInterpolations as $interpolation) {
            $path = str_ireplace(
                $this->startSymbol . $interpolation . $this->endSymbol,
                $$interpolation,
                $path
            );
        }

        return $path;
    }

    /**
     * @param $path
     * @param $what
     * @return bool|int
     */
    public function pathHas($path, $what)
    {
        return strpos($path, $this->startSymbol.$what.$this->endSymbol);
    }

}