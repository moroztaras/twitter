<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Exception\Api\BadRequestJsonHttpException;
use App\Manager\SecurityManager;
use App\Response\SuccessResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 */
#[Route('api/user', name: 'api_user')]
class UserController extends ApiController
{
    public function __construct(
        private SecurityManager $securityManager,
    ) {
    }

    // User login
    #[Route(path: '/login', name: '_login', methods: 'POST')]
    public function login(Request $request): JsonResponse
    {
        if (!($content = $request->getContent())) {
            throw new BadRequestJsonHttpException('Bad Request.');
        }

        return $this->json(['user' => $this->securityManager->userAuthentication($content)], Response::HTTP_OK, [], ['login' => true]);
    }

    // User registration
    #[Route(path: '/register', name: '_register', methods: 'POST')]
    public function register(Request $request): JsonResponse
    {
        if (!($content = $request->getContent())) {
            throw new BadRequestJsonHttpException('Bad Request.');
        }

        return $this->json(['user' => $this->securityManager->create($content)], Response::HTTP_OK, [], ['registration' => true]);
    }

    // Profile user
    #[Route(path: '', name: '_profile', methods: 'GET')]
    public function profile(Request $request): JsonResponse
    {
        return $this->json(['user' => $this->getCurrentUser($request)], Response::HTTP_OK, [], ['profile' => true]);
    }

    // Edit user
    #[Route(path: '', name: '_edit', methods: 'PUT')]
    public function edit(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);

        if (!($content = $request->getContent())) {
            throw new BadRequestJsonHttpException('Bad Request.');
        }

        return $this->json(['user' => $this->securityManager->edit($content, $user)], Response::HTTP_OK);
    }

    // Change password of user
    #[Route(path: '/security', name: '_security', methods: 'POST')]
    public function security(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);

        if (!($content = $request->getContent())) {
            throw new BadRequestJsonHttpException('Bad Request.');
        }
        $this->securityManager->changeEmailAndPassword($user, $content);

        return new SuccessResponse('The user\'s secret data has been successfully changed.');
    }

    // Forgot password of user
    #[Route(path: '/forgot', name: '_forgot_password', methods: 'POST')]
    public function recover(Request $request): JsonResponse
    {
        if (!($content = $request->getContent())) {
            throw new BadRequestJsonHttpException('Bad Request.');
        }

        return $this->json(['message' => $this->securityManager->forgotPassword($content)], Response::HTTP_OK);
    }
}
