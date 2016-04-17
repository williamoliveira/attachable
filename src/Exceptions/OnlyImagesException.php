<?php

namespace Williamoliveira\Attachable\Exceptions;


class OnlyImagesException extends \Exception
{

    /**
     * OnlyImagesException constructor.
     */
    public function __construct()
    {
        parent::__construct("Only images allowed");
    }
}