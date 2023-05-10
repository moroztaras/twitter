<?php

namespace App\Controller\Web;

use App\Components\Form\EntityDeleteForm;
use App\Entity\Twitter;
use App\Entity\User;
use App\Form\Model\TwitterModel;
use App\Form\TwitterType;
use App\Manager\TwitterManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/twitter')]
class TwitterController extends AbstractWebController
{
    public function __construct(
        private TwitterManager $twitterManager,
        private RequestStack $requestStack,
        private PaginatorInterface $paginator
    ) {
    }

    // List all twitter of user
    #[Route('/list', name: 'web_twitter_list', methods: 'GET')]
    public function list(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render(view: 'web/twitter/list.html.twig', parameters: [
            'user' => $user,
            'twitters' => $this->paginator->paginate(
                $this->twitterManager->list($user),
                $request->query->getInt('page', 1),
                $this->getParameter('pagination_limit')
            ),
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
            $this->twitterManager->create($user, $twitterModel, $twitterForm->get('photo')->getData());
            $this->requestStack->getSession()->getFlashBag()->add('success', 'twitter_created_successfully');

            return $this->redirectToRoute('web_twitter_list');
        }

        return $this->render(view: 'web/twitter/new.html.twig', parameters: [
            'form' => $twitterForm->createView(),
            'user' => $user,
        ]);
    }

    // View twitter
    #[Route('/{id}', name: 'web_twitter_view', methods: 'GET')]
    public function view(Twitter $twitter): Response
    {
        return $this->render(view: 'web/twitter/view.html.twig', parameters: [
            'twitter' => $this->twitterManager->show($twitter),
            'user' => $twitter->getUser(),
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
            $this->twitterManager->editTwitter($twitter, $twitterModel, $twitterForm->get('photo')->getData());
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

    #[Route('/{id}/delete', name: 'web_twitter_delete', methods: ['GET', 'POST'])]
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
}
