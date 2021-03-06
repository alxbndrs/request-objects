<?php

declare(strict_types=1);

namespace Fesor\RequestObject\Examples\Request;

use Fesor\RequestObject\ErrorResponseProvider;
use Fesor\RequestObject\RequestObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ResponseProvidingRequest extends RequestObject implements ErrorResponseProvider
{
    public function rules()
    {
        return new Assert\Collection([
            'test' => new Assert\NotBlank(),
        ]);
    }

    public function getErrorResponse(ConstraintViolationListInterface $errors): Response
    {
        return new JsonResponse([
            'message' => 'Please check your data',
            'errors' => array_map(function (ConstraintViolation $violation) {
                return [
                    'path' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }, iterator_to_array($errors)),
        ], 400);
    }
}
