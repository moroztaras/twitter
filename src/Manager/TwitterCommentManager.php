<?php

namespace App\Manager;

use App\Repository\TwitterCommentRepository;
use App\Validator\Helper\ApiObjectValidator;
use Doctrine\Persistence\ManagerRegistry;

class TwitterCommentManager
{
    private const PAGE_LIMIT = 5;

//        /**
//         * TwitterCommentManager constructor.
//         */
//        public function __construct(
//            private ManagerRegistry $doctrine,
//            private ApiObjectValidator $apiObjectValidator,
//            private TwitterCommentRepository $twitterCommentRepository,
//        ) {
//        }
}
