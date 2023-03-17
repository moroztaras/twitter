<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController.
 */
#[Route('/', name: 'app_web_post')]
class PostController extends AbstractController
{
    #[Route('list', name: '_list')]
    public function list(): Response
    {
        return $this->render('web/post/list.html.twig', [
        ]);
    }
}
