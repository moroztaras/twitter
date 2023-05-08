<?php

namespace App\Normalizer;

use App\Entity\TwitterComment;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TwitterCommentsNormalizer implements NormalizerInterface
{
    /**
     * @@param TwitterComment $object object to normalize
     * @param null $format
     *
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        return [
            'uuid' => $object->getUuid(),
            'comment' => $object->getComment(),
            'user' => [
                'uuid' => $object->getUser()->getUuid(),
                'firstName' => $object->getUser()->getFirstName(),
                'lastName' => $object->getUser()->getLastName(),
            ],
            'approved' => $object->isApproved(),
            'createdAt' => $object->getCreatedAt()->format('c'),
            'updatedAt' => $object->getUpdatedAt()->format('c'),
        ];
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($context['edit']) && isset($context['object_to_populate'])) {
            $object = $context['object_to_populate'];

            return $object;
        }
    }

    public function supportsNormalization($twitterComment, $format = null)
    {
        return $twitterComment instanceof TwitterComment;
    }
}
