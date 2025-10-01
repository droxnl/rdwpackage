<?php

namespace Drox\Rdw\Exceptions;

use Exception;

class UnknownLicenseDataException extends Exception
{
    /**
     * @param string $type
     * @param string $license
     */
    public function __construct(string $type, string $license)
    {
        parent::__construct('Rdw api: Invalid license, Type: ' . $type . ' en License:' . $license);
    }
}
