<?php

namespace App\Controller\Api;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TwitterController.
 */
#[Route('api/twitter', name: 'api_twitter')]
class TwitterController extends ApiController
{
    public function __construct()
    {
    }
}
