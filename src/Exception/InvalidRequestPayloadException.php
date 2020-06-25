<?php

declare(strict_types=1);

namespace Fesor\RequestObject\Exception;

use Fesor\RequestObject\RequestObject;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidRequestPayloadException extends \Exception
{
    private RequestObject $requestObject;
    private ConstraintViolationListInterface $errors;

    public function __construct(RequestObject $requestObject, ConstraintViolationListInterface $errors)
    {
        parent::__construct();

        $this->requestObject = $requestObject;
        $this->errors = $errors;
    }

    public function getRequestObject(): RequestObject
    {
        return $this->requestObject;
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
