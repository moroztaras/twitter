<?php

namespace App\Manager;

use App\Entity\User;
use App\Exception\Api\BadRequestJsonHttpException;
use App\Exception\Expected\ExpectedBadRequestJsonHttpException;
use App\Exception\Expected\UserNotFoundException;
use App\Form\Model\Forgot;
use App\Model\LoginModel;
use App\Repository\UserRepository;
use App\Response\SuccessResponse;
use App\Validator\Helper\ApiObjectValidator;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Nonstandard\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;

class SecurityManager
{
    /**
     * SecurityManager constructor.
     */
    public function __construct(
        private ManagerRegistry $doctrine,
        private UserPasswordHasherInterface $passwordEncoder,
        private ApiObjectValidator $apiObjectValidator,
        private EmailManager $emailManager,
        private UserRepository $userRepository,
    ) {
    }

    // Api authentication user
    public function userAuthentication(string $content): User
    {
        /** @var LoginModel $login */
        $login = $this->apiObjectValidator->deserializeAndValidate($content, LoginModel::class, [
            UnwrappingDenormalizer::UNWRAP_PATH => '[login]',
        ]);
        $user = $this->userRepository->findOneByEmail($login->getEmail());

        if (!$user) {
            throw new BadRequestJsonHttpException('Authentication error');
        }

        if ($this->passwordEncoder->isPasswordValid($user, $login->getPlainPassword())) {
            $user->setApiKey(Uuid::uuid4());
            $this->save($user, null);
        }

        return $user;
    }

    // Save user
    public function save(User $user, ?string $plainPassword)
    {
        $this->saveUser($user, $plainPassword);
    }

    // Create Api user
    public function create($content): User
    {
        /** @var User $user */
        $user = $this->apiObjectValidator->deserializeAndValidate($content, User::class, [
            UnwrappingDenormalizer::UNWRAP_PATH => '[user]',
            'registration' => true,
        ]);

        if ($this->doctrine->getRepository(User::class)->findOneBy(['email' => $user->getEmail()])) {
            throw new ExpectedBadRequestJsonHttpException('User already exists.');
        }

        $this->saveUser($user, $user->getPlainPassword());

        return $user;
    }

    // Edit Api user
    public function edit($content, User $user): User
    {
        $validationGroups = ['edit'];
        $this->apiObjectValidator->deserializeAndValidate($content, User::class, [
            AbstractNormalizer::OBJECT_TO_POPULATE => $user,
            AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true,
            UnwrappingDenormalizer::UNWRAP_PATH => '[user]',
        ], $validationGroups);

        $this->saveUser($user, $user->getPlainPassword());

        return $user;
    }

    // Forgot Api password of user
    public function forgotPassword($content): SuccessResponse
    {
        $forgot = $this->apiObjectValidator->deserializeAndValidate($content, Forgot::class, [
            AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true,
            UnwrappingDenormalizer::UNWRAP_PATH => '[forgot]',
        ]);
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['email' => $forgot->getEmail()]);

        if (!$user) {
            throw new UserNotFoundException('No user found with this email address '.$forgot->getEmail());
        }
        $this->emailManager->sendEmailRecoverPassword($user);

        return new SuccessResponse('An email has been sent with a link to reset your password');
    }

    // Save user in DB
    private function saveUser(User $user, string $password = null): void
    {
        if ($password) {
            $user
                ->setPassword($this->passwordEncoder->hashPassword($user, $password))
                ->setTokenRecover(null)
                ->setApiKey(Uuid::uuid4()->toString());
        }

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();
    }
}
