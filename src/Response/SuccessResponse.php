<?php

namespace App\Response;

class SuccessResponse extends JsonResponse
{
    public function __construct($message = 'Success')
    {
        parent::__construct(['message' => $message], 200);
    }
}
