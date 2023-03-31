<?php

namespace App\Manager;

use App\Entity\User;
use App\Exception\Expected\ExpectedBadRequestJsonHttpException;
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
    ) {
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
        $user = $this->apiObjectValidator->deserializeAndValidate($content, User::class, [UnwrappingDenormalizer::UNWRAP_PATH => '[user]', 'registration' => true]);

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
