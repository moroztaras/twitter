<?php

namespace App\Normalizer;

use App\Entity\Twitter;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TwitterNormalizer implements NormalizerInterface
{
    /**
     * @param Twitter $object object to normalize
     * @param null    $format
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        $parentTwitter = [];
        if ($object->getParent()) {
            $parentTwitter[] = [
                'uuid' => $object->getParent()->getUuid(),
                'text' => $object->getParent()->getText(),
                'photo' => $object->getParent()->getPhoto(),
                'video' => $object->getParent()->getVideo(),
                'views' => $object->getParent()->getViews(),
                'status' => $object->getParent()->isStatus(),
                'createdAt' => $object->getParent()->getCreatedAt()->format('c'),
                'updatedAt' => $object->getParent()->getUpdatedAt()->format('c'),
            ];
        }

        return [
            'uuid' => $object->getUuid(),
            'text' => $object->getText(),
            'photo' => $object->getPhoto(),
            'video' => $object->getVideo(),
            'views' => $object->getViews(),
            'status' => $object->isStatus(),
            'is_parent' => (bool) $object->getParent(),
            'parent_twitter' => $parentTwitter,
            'createdAt' => $object->getCreatedAt()->format('c'),
            'updatedAt' => $object->getUpdatedAt()->format('c'),
        ];
    }

    public function supportsNormalization($twitter, $format = null)
    {
        return $twitter instanceof Twitter;
    }
}
