<?php

namespace App\Normalizer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface
{
    /**
     * @param User  $user
     * @param null  $format
     * @param array $context
     *
     * @return array|bool|float|int|string
     */
    public function normalize($user, $format = null, array $context = [])
    {
        if (isset($context['registration']) || isset($context['login'])) {
            return [
                'apiKey' => $user->getApiKey(),
            ];
        }

        if (isset($context['profile'])) {
            return [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'gender' => $user->getGender(),
                'birthday' => $user->getBirthday()->format('d-m-Y'),
            ];
        }
        return [
            'uuid' => $user->getUuid(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'gender' => $user->getGender(),
            'birthday' => $user->getBirthday()->format('d-m-Y'),
            'country' => $user->getCountry(),
            'status' => $user->isStatus(),
        ];
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($context['edit']) && isset($context['object_to_populate'])) {
            $user = $context['object_to_populate'];

            if (isset($data['firstName'])) {
                $user->setFullName($data['firstName']);
            }

            if (isset($data['email'])) {
                $user->setEmail($data['email']);
            }

            if (isset($data['gender'])) {
                $user->setGender($data['gender']);
            }

            if (isset($data['region'])) {
                $user->setRegion($data['region']);
            }

            if (isset($data['birthday'])) {
                $user->setBirtDay($data['birthday']);
            }

            return $user;
        }
    }

    public function supportsNormalization($user, $format = null)
    {
        return $user instanceof User;
    }
}