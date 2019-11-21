<?php

namespace App\Exception;
use Exception;

class NumberZeroIsNotAllowedException extends Exception
{
    public function __construct()
    {
        parent::__construct('The number must be greater than zero.');
    }
}