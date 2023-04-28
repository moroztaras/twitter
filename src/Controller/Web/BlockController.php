<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\UserProfile\Model\UserProfileModel;
use App\Form\UserProfile\UserProfileType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlockController extends AbstractWebController
{

    public function __construct(
       private UserRepository $userRepository
    ) {
    }
    public function userCover($id)
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return new Response();
        }
//        $count_svistyns = $this->getDoctrine()->getRepository(Svistyn::class)->counterSvistynsByUser($user);

        return $this->render('block/cover.html.twig', [
            'user' => $user,
//            'count_svistyns' => $count_svistyns,
        ]);
    }
}
