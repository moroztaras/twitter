<?php

namespace App\Exception\Expected;

use App\Exception\Api\JsonHttpException;

class TwitterNotFoundException extends JsonHttpException
{
    public function __construct($message = 'Twitter not found', $data = null)
    {
        parent::__construct(404, $message, $data);
    }
}
