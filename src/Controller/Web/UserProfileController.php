<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\UserProfile\Model\UserProfileModel;
use App\Form\UserProfile\UserProfileType;
use App\Manager\UserProfileManager;
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
    ) {
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
            $this->profileManager->edit($userProfileModel, $user);
            $this->requestStack->getSession()->getFlashBag()->add('success', 'user_profile_edited_successfully');

            return $this->redirectToRoute('web_twitter_list');
        }

        return $this->render(view: 'web/userProfile/edit.html.twig', parameters: [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
