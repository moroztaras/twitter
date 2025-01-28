<?php

namespace App\Exception\Expected;

class MessageNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Message not found');
    }
}
