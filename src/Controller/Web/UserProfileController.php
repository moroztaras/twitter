<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\Model\UserProfileModel;
use App\Form\UserProfileType;
use App\Manager\UserProfileManager;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/profile')]
class UserProfileController extends AbstractWebController
{
    public function __construct(
        private UserProfileManager $profileManager,
        private RequestStack $requestStack,
        private UserRepository $userRepository
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

        return $this->render('web/userProfile/profile.html.twig', [
            'user' => $user,
        ]);
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

        return $this->render(view: 'web/userProfile/edit.html.twig', parameters: [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
