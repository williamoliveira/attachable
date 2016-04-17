<?php

namespace Williamoliveira\Attachable\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ScopeInterface;

/**
 * Class ModelScope
 * @package Williamoliveira\Attachable\Models
 */
class ModelScope implements ScopeInterface
{

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param Model|AttachableModel $model
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('model', $model->getRealModelName());
    }

    /**
     * Remove the scope from the given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function remove(Builder $builder, Model $model)
    {
        // TODO: Implement remove() method.
    }
}