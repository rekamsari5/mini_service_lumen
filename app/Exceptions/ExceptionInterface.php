<?php

namespace App\Exceptions;

interface ExceptionInterface
{
	public function getData() : mixed;

	public function getErrorCode() : string;
}
