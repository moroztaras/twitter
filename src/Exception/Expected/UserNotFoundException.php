<?php

namespace App\Exception\Expected;

use App\Exception\Api\JsonHttpException;

class UserNotFoundException extends JsonHttpException
{
    public function __construct($message = 'User not found', $data = null)
    {
        parent::__construct(404, $message, $data);
    }
}
