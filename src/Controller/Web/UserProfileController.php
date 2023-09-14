<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\Model\UserProfileModel;
use App\Form\Model\UserProfileSecurityModel;
use App\Form\UserProfileSecurityType;
use App\Form\UserProfileType;
use App\Manager\FriendManager;
use App\Manager\SecurityManager;
use App\Manager\TwitterManager;
use App\Manager\UserProfileManager;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/profile')]
class UserProfileController extends AbstractWebController
{
    public function __construct(
        private readonly UserProfileManager $profileManager,
        private readonly RequestStack $requestStack,
        private readonly UserRepository $userRepository,
        private readonly FriendManager $friendManager,
        private readonly TwitterManager $twitterManager,
        private readonly SecurityManager $securityManager,
        private readonly UserProfileSecurityModel $userProfileSecurityModel,
        private readonly UserPasswordHasherInterface $passwordEncoder,
    ) {
    }

    // User profile view (default)
    #[Route('/', name: 'web_user_profile_default', methods: 'GET')]
    public function default(): RedirectResponse|Response
    {
        $user = $this->getUser();

        return $this->profile($user->getId());
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="web_user_profile", requirements={"id"="\d+"}, defaults={"id" = null})
     */
    // User profile
    public function profile(int $id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            $this->requestStack->getSession()->getFlashBag()->add('danger', 'user_not_found');

            return $this->redirectToRoute('web_user_profile_default');
        }

        return $this->render('web/userProfile/profile.html.twig', $this->renderUserInfo($user));
    }

    // User profile edit
    #[Route('/edit', name: 'web_user_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userProfileModel = (new UserProfileModel())->setUser($user);
        $form = $this->createForm(UserProfileType::class, $userProfileModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->profileManager->edit($userProfileModel, $user, $form->get('avatar')->getData(), $form->get('cover')->getData());
            $this->requestStack->getSession()->getFlashBag()->add('success', 'user_profile_edited_successfully');

            return $this->redirectToRoute('web_user_profile_default');
        }

        return $this->render(view: 'web/userProfile/edit.html.twig', parameters: array_merge(['form' => $form->createView()], $this->renderUserInfo($user)));
    }

    #[Route('/security', name: 'web_user_profile_security')]
    public function security(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $this->userProfileSecurityModel->setUser($user);
        $form = $this->createForm(UserProfileSecurityType::class, $this->userProfileSecurityModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->passwordEncoder->isPasswordValid($user, $this->userProfileSecurityModel->getPassword())) {
                $this->securityManager->changeEmailAndPasswordOfUser($user, $this->userProfileSecurityModel);
                $this->requestStack->getSession()->getFlashBag()->add('success', 'user_change_security_successfully');

                return $this->redirectToRoute('web_user_profile');
            } else {
                $this->requestStack->getSession()->getFlashBag()->add('danger', 'data_is_not_correct');

                return $this->redirectToRoute('web_user_profile_security');
            }
        }

        return $this->render(view: 'web/userProfile/security.html.twig', parameters: array_merge(['form' => $form->createView()], $this->renderUserInfo($user)));
    }

    private function renderUserInfo(User $user): array
    {
        return [
            'user' => $user,
            'following' => $this->friendManager->getCountFollowingsOfUser($user) ?? 0,
            'followers' => $this->friendManager->getCountFollowersOfUser($user, true) ?? 0,
            'twitters' => $this->twitterManager->getCountTwittersOfUser($user) ?? 0,
        ];
    }
}
