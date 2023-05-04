<?php

namespace App\Normalizer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface
{
    /**
     * @@param User $object object to normalize
     * @param null $format
     *
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if (isset($context['registration']) || isset($context['login'])) {
            return [
                'apiKey' => $object->getApiKey(),
            ];
        }

        if (isset($context['profile'])) {
            return [
                'firstName' => $object->getFirstName(),
                'lastName' => $object->getLastName(),
                'email' => $object->getEmail(),
                'gender' => $object->getGender(),
                'birthday' => $object->getBirthday()->format('d-m-Y'),
                'country' => $object->getCountry(),
                'status' => $object->isStatus(),
                'avatar' => $object->getAvatar(),
                'cover' => $object->getCover(),
            ];
        }

        return [
            'uuid' => $object->getUuid(),
            'firstName' => $object->getFirstName(),
            'lastName' => $object->getLastName(),
            'email' => $object->getEmail(),
            'gender' => $object->getGender(),
            'birthday' => $object->getBirthday()->format('d-m-Y'),
            'country' => $object->getCountry(),
            'status' => $object->isStatus(),
        ];
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($context['edit']) && isset($context['object_to_populate'])) {
            $object = $context['object_to_populate'];

            if (isset($data['firstName'])) {
                $object->setFullName($data['firstName']);
            }

            if (isset($data['email'])) {
                $object->setEmail($data['email']);
            }

            if (isset($data['gender'])) {
                $object->setGender($data['gender']);
            }

            if (isset($data['region'])) {
                $object->setRegion($data['region']);
            }

            if (isset($data['birthday'])) {
                $object->setBirtDay($data['birthday']);
            }

            return $object;
        }
    }

    public function supportsNormalization($user, $format = null)
    {
        return $user instanceof User;
    }
}
