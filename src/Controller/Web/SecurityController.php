<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\Security\Model\NewPasswordModel;
use App\Form\Security\Model\RecoverPasswordModel;
use App\Form\Security\NewPasswordType;
use App\Form\Security\RecoverPasswordType;
use App\Form\Security\RegistrationType;
use App\Manager\EmailManager;
use App\Manager\SecurityManager;
use Doctrine\Persistence\ManagerRegistry;
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
    public const REDIRECT_TO_ROUTE = 'app_web_post_list';

    /**
     * SecurityController constructor.
     */
    public function __construct(
        private ManagerRegistry $doctrine,
        private SecurityManager $securityManager,
        private EmailManager $emailManager,
        private RequestStack $requestStack
    ) {
    }

    // User login
    #[Route(path: '/login', name: '_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->requestStack->getSession()->getFlashBag()->add('warning', 'user_log_in');

            return $this->redirectToRoute(self::REDIRECT_TO_ROUTE);
        }

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
        if ($this->getUser()) {
            $this->requestStack->getSession()->getFlashBag()->add('warning', 'user_log_in');

            return $this->redirectToRoute(self::REDIRECT_TO_ROUTE);
        }

        $user = new User();
        $registrationForm = $this->createForm(RegistrationType::class, $user);
        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $this->securityManager->save($user, $user->getPlainPassword());
            $this->requestStack->getSession()->getFlashBag()->add('success', 'user_registration_successfully');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('web/security/registration.html.twig', [
            'register_form' => $registrationForm->createView(),
        ]);
    }

    // Recover password of user
    #[Route('/recover', name: '_recover_password', methods: ['GET', 'POST'])]
    public function recoverPassword(Request $request): Response
    {
        if ($this->getUser()) {
            $this->requestStack->getSession()->getFlashBag()->add('warning', 'user_log_in');

            return $this->redirectToRoute(self::REDIRECT_TO_ROUTE);
        }

        $recoverPasswordModel = new RecoverPasswordModel();
        $recoverPasswordForm = $this->createForm(RecoverPasswordType::class, $recoverPasswordModel);
        $recoverPasswordForm->handleRequest($request);
        if ($recoverPasswordForm->isSubmitted() && $recoverPasswordForm->isValid()) {
            $user = $this->doctrine->getRepository(User::class)->findOneBy(['email' => $recoverPasswordModel->getEmail()]);
            if ($user) {
                $status = $this->emailManager->sendEmailRecoverPassword($user);
                $this->requestStack->getSession()->getFlashBag()->add('warning', 'user_recover_password_send_email');
            } else {
                $this->requestStack->getSession()->getFlashBag()->add('danger', 'user_not_found');
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('web/security/recover.html.twig', [
            'recover_form' => $recoverPasswordForm->createView(),
        ]);
    }

    // Set new password of user
    #[Route('/new-password/{email}/{token}', name: '_new_password', methods: ['GET', 'POST'])]
    public function newPassword($email, $token, Request $request): Response
    {
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['email' => $email, 'tokenRecover' => $token]);
        if (!$user) {
            $this->requestStack->getSession()->getFlashBag()->add('danger', 'user_not_found');

            return $this->redirectToRoute('app_recover_password');
        }
        $newPasswordModel = new NewPasswordModel();
        $newPasswordForm = $this->createForm(NewPasswordType::class, $newPasswordModel);
        $newPasswordForm->handleRequest($request);
        if ($newPasswordForm->isSubmitted() && $newPasswordForm->isValid()) {
            $this->securityManager->save($user, $newPasswordModel->getPlainPassword());
            $this->requestStack->getSession()->getFlashBag()->add('success', 'user_new_password_changed_successfully');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('web/security/new_password.html.twig', [
            'new_password_form' => $newPasswordForm->createView(),
        ]);
    }

    // User logout
    #[Route(path: '/logout', name: '_logout')]
    public function logout(): Response
    {
    }
}
