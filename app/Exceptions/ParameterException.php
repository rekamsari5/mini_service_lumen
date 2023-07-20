<?php

namespace App\Exceptions;

use \StdClass;
use Exception;
use App\Exceptions\ExceptionInterface;

class ParameterException extends Exception implements ExceptionInterface
{
    /* Properties */
    private mixed $data;
    private string $errorCode;

	// Redefine the exception so message isn't optional
    public function __construct($message = 'Parameter tidak sesuai', $data = null)
    {
        $this->data = $data;
        $this->errorCode = '01';
    	parent::__construct($message, 1, null);
    }

    public function getData() : mixed
    {
        return $this->data;
    }

    public function getErrorCode() : string
    {
        return $this->errorCode;
    }
}
