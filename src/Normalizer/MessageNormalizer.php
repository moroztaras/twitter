<?php

namespace App\Normalizer;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MessageNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param Message $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $jsonObject = [
            'uuid' => (string) $object->getUuid(),
            'status' => (bool) $object->getStatus(),
            'message' => (string) $object->getMessage(),
            'sender' => (array) $this->getUser($object->getSender()),
            'receiver' => (array) $this->getUser($object->getReceiver()),
            'createdAt' => $object->getCreatedAt()->format('c'),
            'editedAt' => $object->getUpdatedAt()->format('c'),
        ];

        return $jsonObject;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Message;
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
