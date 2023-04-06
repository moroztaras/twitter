<?php

namespace App\Exception\Expected;

class ExpectedBadRequestJsonHttpException extends AbstractExpectedJsonHttpException
{
    public function __construct($message = 'Bad Request', $data = null)
    {
        parent::__construct(400, $message, $data);
    }
}
