<?php

namespace App\Exception;
use Exception;

class NumberIsNotAllowedException extends Exception
{
    public function __construct()
    {
        parent::__construct('The number must be between 1 and 70.');
    }
}