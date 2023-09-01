<?php

namespace App\Controller\Web;

use App\Components\Form\EntityDeleteForm;
use App\Entity\Twitter;
use App\Entity\User;
use App\Form\Model\TwitterModel;
use App\Form\TwitterType;
use App\Manager\TwitterManager;
use App\Repository\TwitterCommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TwitterController extends AbstractWebController
{
    private const LIMIT_TWITTERS = 30;

    public function __construct(
        private TwitterManager $twitterManager,
        private TwitterCommentRepository $twitterCommentRepository,
        private RequestStack $requestStack,
        private PaginatorInterface $paginator,
        private TwitterModel $twitterModel,
    ) {
    }

    #[Route('/twitter/list', name: 'web_twitter_list', methods: 'GET')]
    public function list(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->renderTwittersList($request, $user);
    }

    #[Route('/twitter/following', name: 'web_twitter_following', methods: ['GET'])]
    public function twittersOfFollowings(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('web/twitter/feed.html.twig', [
            'twitters' => $this->paginator->paginate(
                $this->twitterManager->twittersOfFollowing($user, self::LIMIT_TWITTERS),
                $request->query->getInt('page', 1),
                $this->getParameter('pagination_limit')
            ),
            'user' => $user,
        ]);
    }

    #[Route('user/{id}/twitter/list', name: 'user_twitter_list', requirements: ['id' => '\d+'], methods: 'GET')]
    public function userTwittersList(Request $request, User $user): Response
    {
        return $this->renderTwittersList($request, $user);
    }

    #[Route('/twitter/new', name: 'web_twitter_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $twitterModel = new TwitterModel();
        $twitterForm = $this->createForm(TwitterType::class, $twitterModel);
        $twitterForm->handleRequest($request);

        if ($twitterForm->isSubmitted() && $twitterForm->isValid()) {
            $this->twitterManager->create($user, $twitterModel, $twitterForm->get('photo')->getData());
            $this->requestStack->getSession()->getFlashBag()->add('success', 'twitter_created_successfully');

            return $this->redirectToRoute('web_twitter_list');
        }

        return $this->render(view: 'web/twitter/new.html.twig', parameters: [
            'form' => $twitterForm->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/twitter/{id}', name: 'web_twitter_view', methods: 'GET')]
    public function view(Request $request, Twitter $twitter): Response
    {
        return $this->render(view: 'web/twitter/view.html.twig', parameters: [
            'twitter' => $this->twitterManager->show($twitter),
            'user' => $twitter->getUser(),
            'comments' => $this->paginator->paginate(
                $this->twitterCommentRepository->getlistCommentsByTwitter($twitter),
                $request->query->getInt('page', 1),
                $this->getParameter('pagination_limit')
            ),
        ]);
    }

    #[Route('/twitter/{id}/edit', name: 'web_twitter_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Twitter $twitter): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($twitter->getUser()->getUuid() !== $user->getUuid()) {
            $this->requestStack->getSession()->getFlashBag()->add('warning', 'twitter_edit_is_forbidden');

            return $this->redirectToRoute('web_twitter_view', [
                'id' => $twitter->getId(),
            ]);
        }
        $this->twitterModel->setEntityTwitter($twitter);
        $twitterForm = $this->createForm(TwitterType::class, $this->twitterModel);
        $twitterForm->handleRequest($request);

        if ($twitterForm->isSubmitted() && $twitterForm->isValid()) {
            $this->twitterManager->editTwitter($twitter, $this->twitterModel, $twitterForm->get('photo')->getData());
            $this->requestStack->getSession()->getFlashBag()->add('success', 'twitter_edited_successfully');

            return $this->redirectToRoute('web_twitter_view', [
                'id' => $twitter->getId(),
            ]);
        }

        return $this->render(view: 'web/twitter/edit.html.twig', parameters: [
            'form' => $twitterForm->createView(),
            'twitter' => $twitter,
            'user' => $twitter->getUser(),
        ]);
    }

    #[Route('/twitter/{id}/share', name: 'web_twitter_share', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function share(Request $request, Twitter $twitter): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$twitter || $twitter->getUser()->getId() == $user->getId() || null != $twitter->getParent()) {
            return $this->redirectToRoute('user_twitter_list', ['id' => $twitter->getUser()->getId()]);
        }
        $shareTwitter = $this->twitterManager->createNewTwitterForShare($twitter, $user);
        $this->twitterModel->setEntityTwitter($shareTwitter);

        $twitterForm = $this->createForm(TwitterType::class, $this->twitterModel);
        $twitterForm->handleRequest($request);
        if ($twitterForm->isSubmitted() && $twitterForm->isValid()) {
            $shareTwitter = $this->twitterManager->editTwitter($shareTwitter, $this->twitterModel);
            $this->requestStack->getSession()->getFlashBag()->add('success', 'twitter_shared_successfully');

            return $this->redirectToRoute('web_twitter_view', [
                'id' => $shareTwitter->getId(),
            ]);
        }

        return $this->render('web/twitter/share.html.twig', [
            'form' => $twitterForm->createView(),
            'twitter' => $shareTwitter,
            'user' => $user,
        ]);
    }

    #[Route('/twitter/{id}/delete', name: 'web_twitter_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Twitter $twitter): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$twitter || !$twitter->getUser() || $twitter->getUser()->getId() != $user->getId()) {
            $this->requestStack->getSession()->getFlashBag()->add('danger', 'delete_twitter_is_forbidden');

            return $this->redirectToRoute('web_twitter_list');
        }

        $form = $this->createForm(EntityDeleteForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->twitterManager->remove($twitter);
            $this->requestStack->getSession()->getFlashBag()->add('danger', 'twitter_was_deleted_successfully');

            return $this->redirectToRoute('web_twitter_list');
        }

        return $this->render('web/twitter/delete.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    public function countCommentsOfTwitter(Twitter $twitter): Response
    {
        return new Response('5');
    }

    private function renderTwittersList(Request $request, User $user): Response
    {
        return $this->render(view: 'web/twitter/list.html.twig', parameters: [
            'user' => $user,
            'twitters' => $this->paginator->paginate(
                $this->twitterManager->list($user),
                $request->query->getInt('page', 1),
                $this->getParameter('pagination_limit')
            ),
        ]);
    }
}
