<?php

namespace App\Exception;
use Exception;

class NoFilesFoundException extends Exception
{
    public function __construct($pathToFiles)
    {
        parent::__construct(sprintf('No files found in %s', $pathToFiles));
    }
}