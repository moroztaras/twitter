<?php

namespace App\Normalizer;

use App\Entity\Dialogue;
use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DialogueNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param Dialogue $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $jsonObject = [
            'uuid' => (string) $object->getUuid(),
            'creator' => $this->getUser($object->getCreator()),
            'receiver' => $this->getUser($object->getReceiver()),
        ];

        return $jsonObject;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Dialogue;
    }

    private function getUser(User $user): array
    {
        return [
            'uuid' => (string) $user->getUuid(),
            'firstName' => (string) $user->getFirstName(),
            'lastName' => (string) $user->getLastName(),
        ];
    }
}
