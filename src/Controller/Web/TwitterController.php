<?php

namespace App\Controller\Web;

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
    #[Route('/list', name: 'web_twitter_list')]
    public function list(): Response
    {
        $user = $this->getUser();

        return $this->render(view: 'web/twitter/list.html.twig', parameters: [
            'user' => $user,
            'twitters' => $this->twitterManager->list($user),
        ]);
    }

    // Create new twitter
    #[Route('/new', name: 'web_twitter_new')]
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
}
