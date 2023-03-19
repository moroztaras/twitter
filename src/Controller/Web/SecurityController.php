<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\LoginType;
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
#[Route('', name: 'app_web_security')]
class SecurityController extends AbstractController
{
    /**
     * SecurityController constructor.
     */
    public function __construct(
        private SecurityManager $securityManager,
//        private FlashBagInterface $flashBag,
        private RequestStack $requestStack
    ) {
    }

    // User login
    #[Route('/login', name: '_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = new User();
        $user->setEmail($authenticationUtils->getLastUsername());
        $form = $this->createForm(LoginType::class, $user, [
//            'action' => $this->generateUrl('login_check'),
        ]);

        return $this->render('web/security/login.html.twig', [
            'form' => $form->createView(),
        ]);
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

            return $this->redirectToRoute('app_web_security_login');
        }

        return $this->render('web/security/registration.html.twig', [
            'register_form' => $registrationForm->createView(),
        ]);
    }
}
