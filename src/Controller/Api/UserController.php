<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Exception\Api\BadRequestJsonHttpException;
use App\Exception\Expected\ExpectedBadRequestJsonHttpException;
use App\Manager\SecurityManager;
use App\Validator\Helper\ApiObjectValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;

/**
 * Class UserController.
 */
#[Route('api/user', name: 'api_user')]
class UserController extends ApiController
{
    public function __construct(
        private ApiObjectValidator $apiObjectValidator,
        private SecurityManager $securityManager,
    ) {
    }

    #[Route(path: '/register', name: '_register', methods: 'POST')]
    public function register(Request $request): JsonResponse
    {
        if (!($content = $request->getContent())) {
            throw new BadRequestJsonHttpException('Bad Request.');
        }
        /** @var User $user */
        $user = $this->apiObjectValidator->deserializeAndValidate($content, User::class, [UnwrappingDenormalizer::UNWRAP_PATH => '[user]', 'registration' => true]);

        if ($this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $user->getEmail()])) {
            throw new ExpectedBadRequestJsonHttpException('User already exists.');
        }

        $this->securityManager->save($user, $user->getPlainPassword());

        return $this->json([
            'user' => $user,
        ]);
    }

    #[Route(path: '', name: '_profile', methods: 'GET')]
    public function profile(Request $request): JsonResponse
    {
        return $this->json([
            'user' => $this->getCurrentUser($request),
        ], Response::HTTP_OK);
    }

    #[Route(path: '', name: '_edit', methods: 'PUT')]
    public function edit(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);

        if (!($content = $request->getContent())) {
            throw new BadRequestJsonHttpException('Bad Request.');
        }
        $validationGroups = ['edit'];
        $this->apiObjectValidator->deserializeAndValidate($content, User::class, [
            AbstractNormalizer::OBJECT_TO_POPULATE => $user,
            AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true,
            UnwrappingDenormalizer::UNWRAP_PATH => '[user]',
        ], $validationGroups);

        $this->securityManager->save($user, $user->getPlainPassword());

        return $this->json(['user' => $user], Response::HTTP_OK);
    }
}
