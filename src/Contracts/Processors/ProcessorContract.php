<?php

namespace Williamoliveira\Attachable\Contracts\Processors;


use Williamoliveira\Attachable\Models\AttachableModel;

/**
 * Interface ProcessorContract
 * @package Williamoliveira\Attachable\Contracts
 */
interface ProcessorContract
{
    /**
     * @param AttachableModel $model
     * @return mixed
     */
    public function store(AttachableModel $model);

    /**
     * @param AttachableModel $model
     * @return mixed
     */
    public function update(AttachableModel $model);

    /**
     * @param AttachableModel $model
     * @return mixed
     */
    public function delete(AttachableModel $model);
}