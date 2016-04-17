<?php

namespace Williamoliveira\Attachable\Contracts;


use Williamoliveira\Attachable\Models\AttachableModel;

interface InterpolatorContract
{
    /**
     * @param AttachableModel $model
     * @param string $templateName
     * @return string
     */
    public function parsePath(AttachableModel $model, $templateName = 'original');

    /**
     * @param AttachableModel $model
     * @return string
     */
    public function parseFilePath(AttachableModel $model);

    /**
     * @param AttachableModel $model
     * @param string $template
     * @return string
     */
    public function parseImagePath(AttachableModel $model, $template = 'original');

    /**
     * @param $path
     * @param $what
     * @return bool|int
     */
    public function pathHas($path, $what);
}