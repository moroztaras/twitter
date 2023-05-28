<?php

namespace App\Controller\Web;

use App\Entity\Twitter;
use App\Entity\User;
use App\Form\Model\TwitterCommentModel;
use App\Form\TwitterCommentType;
use App\Manager\TwitterCommentManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TwitterCommentController extends AbstractWebController
{
    public function __construct(
        private readonly TwitterCommentManager $twitterCommentManager,
        private readonly RequestStack $requestStack)
    {
    }

    #[Route('/twitter/{id_twitter}/comment/create', name: 'create_new_comment', requirements: ['id_twitter' => "\d+"], methods: 'POST')]
    #[ParamConverter('twitter', class: Twitter::class, options: ['mapping' => ['id_twitter' => 'id']])]
    public function create(Request $request, Twitter $twitter): Response|RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $twitterCommentModel = new TwitterCommentModel();
        $form = $this->createForm(TwitterCommentType::class, $twitterCommentModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->twitterCommentManager->createNewComment($twitter, $user, $twitterCommentModel);
            $this->requestStack->getSession()->getFlashBag()->add('success', 'comment_created_successfully');

            return $this->redirectToRoute('web_twitter_view', ['id' => $twitter->getId()]);
        }

        return $this->render('web/twitterComment/create.html.twig', [
            'user' => $this->getUser(),
            'twitter' => $twitter,
            'form' => $form->createView(),
        ]);
    }
}
