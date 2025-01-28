<?php

namespace App\Controller\Web;

use App\Components\Form\EntityDeleteForm;
use App\Entity\Twitter;
use App\Entity\TwitterComment;
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

#[Route('{_locale<%app.supported_locales%>}')]
class TwitterCommentController extends AbstractWebController
{
    public function __construct(
        private readonly TwitterCommentManager $twitterCommentManager,
        private readonly RequestStack $requestStack)
    {
    }

    #[Route('/twitter/{id_twitter}/comment/create', name: 'web_create_new_twitter_comment',
        requirements: ['id_twitter' => "\d+"], methods: 'POST')]
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

    #[Route('/twitter/{id_twitter}/comment/{id_comment}/edit', name: 'web_edit_twitter_comment',
        requirements: ['id_twitter' => '\d+', 'id_comment' => '\d+'], methods: ['GET', 'POST'])]
    #[ParamConverter('twitter', class: Twitter::class, options: ['mapping' => ['id_twitter' => 'id']])]
    #[ParamConverter('comment', class: TwitterComment::class, options: ['mapping' => ['id_comment' => 'id']])]
    public function edit(Request $request, Twitter $twitter, TwitterComment $comment): Response|RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$comment || $comment->getUser() !== $user) {
            $this->requestStack->getSession()->getFlashBag()->add('danger', 'edit_comment_is_forbidden');

            return $this->redirectToRoute('web_twitter_view', ['id' => $twitter->getId()]);
        }

        $twitterCommentModel = new TwitterCommentModel();
        $twitterCommentModel->setEntityTwitterComment($comment);
        $form = $this->createForm(TwitterCommentType::class, $twitterCommentModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->twitterCommentManager->editComment($comment, $twitterCommentModel);

            $this->requestStack->getSession()->getFlashBag()->add('success', 'comment_edited_successfully');

            return $this->redirect($this->generateUrl('web_twitter_view', [
                'id' => $comment->getTwitter()->getId(),
            ]).'#comment-'.$comment->getId()
            );
        }

        return $this->render('web/twitterComment/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/twitter/{id_twitter}/comment/{id_comment}/delete', name: 'web_twitter_comment_delete',
        requirements: ['id_twitter' => '\d+', 'id_comment' => '\d+'], methods: ['POST', 'GET'])]
    #[ParamConverter('twitter', class: Twitter::class, options: ['mapping' => ['id_twitter' => 'id']])]
    #[ParamConverter('comment', class: TwitterComment::class, options: ['mapping' => ['id_comment' => 'id']])]
    public function delete(Request $request, Twitter $twitter, TwitterComment $comment): Response|RedirectResponse
    {
        if ($comment->getUser() !== $this->getUser() || $twitter->getUser() !== $comment->getUser()) {
            $this->requestStack->getSession()->getFlashBag()->add('danger', 'delete_comment_is_forbidden');
        }

        $form = $this->createForm(EntityDeleteForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->twitterCommentManager->removeComment($comment);
            $this->requestStack->getSession()->getFlashBag()->add('danger', 'comment_was_deleted');

            return $this->redirect($this->generateUrl('web_twitter_view', ['id' => $twitter->getId()]));
        }

        return $this->render('web/twitterComment/delete.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'twitter' => $twitter,
        ]);
    }
}
