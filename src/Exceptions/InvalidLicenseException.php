<?php

namespace DroxNL\Rdw\Exceptions;

use Exception;

class InvalidLicenseException extends Exception
{
    /**
     * @param $license
     */
    public function __construct($license)
    {
        parent::__construct('Rdw api: Invalid or unknown license: '. $license);
    }
}
