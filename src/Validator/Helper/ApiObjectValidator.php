<?php

namespace App\Validator\Helper;

use App\Exception\Helper\BadRequestJsonHttpException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiObjectValidator
{
    public function __construct(
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
        //        private LoggerInterface $logger
    ) {
    }

    public function deserializeAndValidate($data, $class, array $context = [], array $groups = []): object
    {
        //        if (!isset($context[AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE])) {
        //            $context[AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE] = true;
        //        }

        try {
            $object = $this->serializer->deserialize($data, $class, 'json', $context);
        } catch (UnexpectedValueException|\TypeError $e) {
            throw new BadRequestJsonHttpException('Invalid json.', 400, ['message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            throw new BadRequestJsonHttpException('Invalid json.', 400, ['message' => $e->getMessage()]);
        }

        $errors = $this->validator->validate($object, null, $groups);

        if ($errors->count()) {
            $outErrors = [];
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                foreach ($error->getParameters() as $key => $parameter) {
                    $outErrors[$key] = $parameter;
                }
            }
            throw new BadRequestJsonHttpException(isset($error) ? $error->getMessage() : 'Bad request', 400, $outErrors);
        }

        return $object;
    }
}
