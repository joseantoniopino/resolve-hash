<?php

namespace App\Exception;
use Exception;

class FileNoExistException extends Exception
{
    public function __construct($filename)
    {
        parent::__construct(sprintf('The %s not exist', $filename));
    }
}