<?php

namespace App\Controller\Api;

/**
 * Class TwitterCommentController.
 */
class TwitterCommentController extends ApiController
{
    public function __construct(
        private TwitterCommentManager $twitterCommentManager,
    ) {
    }
}
