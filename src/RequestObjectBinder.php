<?php

declare(strict_types=1);

namespace Fesor\RequestObject;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestObjectBinder
{
    private ValidatorInterface $validator;
    private PayloadResolver $payloadResolver;
    private ?ErrorResponseProvider $errorResponseProvider;

    public function __construct(
        PayloadResolver $payloadResolver,
        ValidatorInterface $validator,
        ErrorResponseProvider $errorResponseProvider = null
    ) {
        $this->validator = $validator;
        $this->payloadResolver = $payloadResolver;
        $this->errorResponseProvider = $errorResponseProvider;
    }

    public function bind(Request $request, callable $action): ?Response
    {
        $matchedArguments = $this->matchActionArguments($action);
        if (!array_key_exists('requestObject', $matchedArguments)) {
            return null;
        }

        $requestObjectClass = $matchedArguments['requestObject']->getClass()->name;
        /** @var RequestObject $requestObject */
        $requestObject = new $requestObjectClass();
        $request->attributes->set(
            $matchedArguments['requestObject']->name,
            $requestObject
        );

        $payload = $this->resolvePayload($requestObject, $request);

        $errors = $this->validator->validate(
            $payload,
            $requestObject->rules(),
            $requestObject->validationGroup($payload)
        );

        $requestObject->setPayload($payload);
        if (array_key_exists('errors', $matchedArguments)) {
            $request->attributes->set($matchedArguments['errors']->name, $errors);
        } elseif ($errors && 0 !== $errors->count()) {
            return $this->providerErrorResponse($requestObject, $errors);
        }

        return null;
    }

    /**
     * @throws InvalidRequestPayloadException
     */
    private function providerErrorResponse(RequestObject $requestObject, ConstraintViolationListInterface $errors): Response
    {
        if ($requestObject instanceof ErrorResponseProvider) {
            return $requestObject->getErrorResponse($errors);
        }

        if ($this->errorResponseProvider) {
            return $this->errorResponseProvider->getErrorResponse($errors);
        }

        throw new InvalidRequestPayloadException($requestObject, $errors);
    }

    private function matchActionArguments(callable $action): array
    {
        if (is_array($action)) {
            $classReflection = new \ReflectionClass($action[0]);
            $actionReflection = $classReflection->getMethod($action[1]);
        } elseif ($action instanceof \Closure || is_string($action)) {
            $actionReflection = new \ReflectionFunction($action);
        } else {
            $classReflection = new \ReflectionClass($action);
            $actionReflection = $classReflection->getMethod('__invoke');
        }

        $matchedArguments = [];
        $arguments = $actionReflection->getParameters();
        foreach ($arguments as $argument) {
            if ($this->isArgumentIsSubtypeOf($argument, RequestObject::class)) {
                $matchedArguments['requestObject'] = $argument;
            }
            if ($this->isArgumentIsSubtypeOf($argument, ConstraintViolationListInterface::class)) {
                $matchedArguments['errors'] = $argument;
            }
        }

        return $matchedArguments;
    }

    private function resolvePayload(RequestObject $requestObject, Request $request): array
    {
        if ($requestObject instanceof PayloadResolver) {
            return $requestObject->resolvePayload($request);
        }

        return $this->payloadResolver->resolvePayload($request);
    }

    private function isArgumentIsSubtypeOf(\ReflectionParameter $argument, $subtype): bool
    {
        if (!($className = $argument->getClass())) {
            return false;
        }

        return is_a($className->name, $subtype, true);
    }
}
