<?php

namespace App\Exception;
use Exception;

class NotFindFilesException extends Exception
{
    public function __construct($pathToFiles)
    {
        parent::__construct(sprintf('No files found in %s', $pathToFiles));
    }
}