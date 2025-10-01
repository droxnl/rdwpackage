<?php

namespace DroxNL\Rdw\Exceptions;

use Exception;

class UnreachableEndpointException extends Exception
{
    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        parent::__construct('Rdw api: API endpoint (' . $type . ') not available');
    }
}
