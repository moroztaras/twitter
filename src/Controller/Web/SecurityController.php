<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Manager\SecurityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController.
 */
#[Route('', name: 'app')]
class SecurityController extends AbstractController
{
    /**
     * SecurityController constructor.
     */
    public function __construct(
        private SecurityManager $securityManager,
        private RequestStack $requestStack
    ) {
    }

    // User login
    #[Route(path: '/login', name: '_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('web/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    // User registration
    #[Route('/registration', name: '_registration', methods: ['GET', 'POST'])]
    public function registration(Request $request): Response
    {
        $user = new User();
        $registrationForm = $this->createForm(RegistrationType::class, $user);
        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $this->securityManager->create($user);
            $this->requestStack->getSession()->getFlashBag()->add('success', 'user_registration_successfully');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('web/security/registration.html.twig', [
            'register_form' => $registrationForm->createView(),
        ]);
    }

    #[Route(path: '/logout', name: '_logout')]
    public function logout(): Response
    {
    }
}
