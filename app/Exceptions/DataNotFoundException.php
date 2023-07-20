<?php

namespace App\Exceptions;

use \StdClass;
use Exception;
use App\Exceptions\ExceptionInterface;


class DataNotFoundException extends Exception implements ExceptionInterface
{
    private mixed $data;
    private String $errorCode;

    // Redefine the exception so message isn't optional
    public function __construct($message = 'Data tidak ditemukan', $data = null)
    {
        $this->data = $data;
        $this->errorCode = '02';
        parent::__construct($message, 2, null);
    }

    public function getData() : mixed
    {
        return $this->data;
    }

    public function getErrorCode() : String
    {
        return $this->errorCode;
    }
}
