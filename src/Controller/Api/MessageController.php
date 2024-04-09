<?php

namespace App\Controller\Api;

use App\Manager\MessageManager;

class MessageController extends ApiController
{
    public function __construct(
        private readonly MessageManager $messageManager,
    ) {
    }
}
