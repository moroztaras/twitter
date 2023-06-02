<?php

namespace App\Exception\Helper;

use App\Exception\Expected\AbstractExpectedJsonHttpException;

class BadRequestJsonHttpException extends AbstractExpectedJsonHttpException
{
    public function __construct($message, $statusCode = 400, $data = null)
    {
        parent::__construct($statusCode, $message, $data);
    }
}
