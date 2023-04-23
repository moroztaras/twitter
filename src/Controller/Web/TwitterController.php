<?php

namespace App\Controller\Web;

use App\Entity\Twitter;
use App\Entity\User;
use App\Form\Twitter\Model\TwitterModel;
use App\Form\Twitter\TwitterType;
use App\Manager\TwitterManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/twitter')]
class TwitterController extends AbstractWebController
{
    public function __construct(
        private TwitterManager $twitterManager,
        private RequestStack $requestStack
    ) {
    }

    // List all twitter of user
    #[Route('/list', name: 'web_twitter_list', methods: 'GET')]
    public function list(): Response
    {
        $user = $this->getUser();

        return $this->render(view: 'web/twitter/list.html.twig', parameters: [
            'user' => $user,
            'twitters' => $this->twitterManager->list($user),
        ]);
    }

    // Create new twitter
    #[Route('/new', name: 'web_twitter_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $twitterModel = new TwitterModel();
        $twitterForm = $this->createForm(TwitterType::class, $twitterModel);
        $twitterForm->handleRequest($request);

        if ($twitterForm->isSubmitted() && $twitterForm->isValid()) {
            $this->twitterManager->create($user, $twitterModel);
            $this->requestStack->getSession()->getFlashBag()->add('success', 'twitter_created_successfully');

            return $this->redirectToRoute('web_twitter_list');
        }

        return $this->render(view: 'web/twitter/new.html.twig', parameters: [
            'form' => $twitterForm->createView(),
        ]);
    }

    // View twitter
    #[Route('/{id}', name: 'web_twitter_view', methods: 'GET')]
    public function view(Twitter $twitter): Response
    {
        return $this->render(view: 'web/twitter/view.html.twig', parameters: [
            'twitter' => $this->twitterManager->show($twitter),
        ]);
    }

    // Edit twitter
    #[Route('/{id}/edit', name: 'web_twitter_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Twitter $twitter): Response
    {
        $twitterModel = new TwitterModel();
        $twitterModel->setEntityTwitter($twitter);
        $twitterForm = $this->createForm(TwitterType::class, $twitterModel);
        $twitterForm->handleRequest($request);

        if ($twitterForm->isSubmitted() && $twitterForm->isValid()) {
            $this->twitterManager->editTwitter($twitter, $twitterModel);
            $this->requestStack->getSession()->getFlashBag()->add('success', 'twitter_edited_successfully');

            return $this->redirectToRoute('web_twitter_view', [
                'id' => $twitter->getId(),
            ]);
        }

        return $this->render(view: 'web/twitter/edit.html.twig', parameters: [
            'form' => $twitterForm->createView(),
            'twitter' => $twitter,
        ]);
    }
}
