<?php

namespace App\Exceptions;

use \StdClass;
use \Exception;
use App\Exceptions\ExceptionInterface;

class InvalidRuleException extends Exception implements ExceptionInterface {
    /* Properties */
    private mixed $data;
    private string $errorCode;

    // Redefine the exception so message isn't optional
    public function __construct($message = 'Validasi tidak terpenuhi', $data = null)
    {
        $this->data = $data;
        $this->errorCode = '03';
        parent::__construct($message, 3, null);
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
