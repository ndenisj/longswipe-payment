<?php

namespace Longswipe\Payment\Exceptions;

use Exception;

class LongswipeException extends Exception
{
    private $errorData;

    public function __construct($message = "", $code = 0, $errorData = null)
    {
        parent::__construct($message, $code);
        $this->errorData = $errorData;
    }

    public function getErrorData()
    {
        return $this->errorData;
    }
}

