<?php

namespace App\Normalizer;

use App\Entity\Friend;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FriendNormalizer implements NormalizerInterface
{
    /**
     * @@param Friend $object object to normalize
     * @param null $format
     *
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if (isset($context['following'])) {
            $data = [
                'following' => [
                    'uuid' => $object->getFriend()->getUuid(),
                    'firstName' => $object->getFriend()->getFirstName(),
                    'lastName' => $object->getFriend()->getLastName(),
                    'avatar' => $object->getFriend()->getAvatar(),
                    'blocked' => $object->getFriend()->isStatus(),
                    'birthday' => $object->getFriend()->getBirthday()->format('d-m-Y'),
                    'createdAt' => $object->getFriend()->getCreatedAt()->format('d-m-Y'),
                ],
            ];
        }
        if (isset($context['followers'])) {
            $data = [
                'follower' => [
                    'uuid' => $object->getUser()->getUuid(),
                    'firstName' => $object->getUser()->getFirstName(),
                    'lastName' => $object->getUser()->getLastName(),
                    'avatar' => $object->getUser()->getAvatar(),
                    'blocked' => $object->getUser()->isStatus(),
                    'birthday' => $object->getUser()->getBirthday()->format('d-m-Y'),
                    'createdAt' => $object->getUser()->getCreatedAt()->format('d-m-Y'),
                ],
            ];
        }

        return array_merge_recursive(
            $data ?? [],
            [
                'status' => $object->getStatus(),
                'friendShipAt' => $object->getCreatedAt()->format('d-m-Y'),
            ]
        );
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($context['edit']) && isset($context['object_to_populate'])) {
            $object = $context['object_to_populate'];

            return $object;
        }
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Friend;
    }
}
