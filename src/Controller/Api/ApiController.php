<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Exception\Api\BadRequestJsonHttpException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiController.
 */
abstract class ApiController extends AbstractController
{
    protected ManagerRegistry $doctrine;

    /**
     * @required
     */
    public function setDoctrine(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    protected function getDoctrine(): ManagerRegistry
    {
        return $this->doctrine;
    }

//    protected function getUser(): ?User
//    {
//        $user = parent::getUser();
//
//        return $user instanceof User ? $user : null;
//    }

    protected function getCurrentUser(Request $request): ?User
    {
        $user = $this->doctrine->getManager()->getRepository(User::class)->findOneBy([
            'apiKey' => $request->headers->get('x-api-key'),
        ]);

        if (!$user) {
            throw new BadRequestJsonHttpException(Response::HTTP_UNAUTHORIZED, 'User Not Unauthorized');
        }

        return $user instanceof User ? $user : null;
    }
}
