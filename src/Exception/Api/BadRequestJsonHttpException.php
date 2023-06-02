<?php

namespace App\Exception\Api;

class BadRequestJsonHttpException extends JsonHttpException
{
    public function __construct($message = 'Bad Request', $data = null)
    {
        parent::__construct(400, $message, $data);
    }
}
