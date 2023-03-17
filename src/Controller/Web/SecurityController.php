<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\LoginType;
use App\Manager\SecurityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController.
 */
#[Route('/user', name: 'app_web_security_user')]
class SecurityController extends AbstractController
{
    /**
     * SecurityController constructor.
     */
    public function __construct(
        private SecurityManager $securityManager,
    ) {
    }

    #[Route('/login', name: '_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = new User();
        $user->setEmail($authenticationUtils->getLastUsername());
        $form = $this->createForm(LoginType::class, $user, [
//            'action' => $this->generateUrl('login_check'),
        ]);

        return $this->render('web/user/security/login.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
