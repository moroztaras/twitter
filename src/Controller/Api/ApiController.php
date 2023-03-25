<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    protected function getUser(): ?User
    {
        $user = parent::getUser();

        return $user instanceof User ? $user : null;
    }
}
