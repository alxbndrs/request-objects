<?php

declare(strict_types=1);

namespace Fesor\RequestObject;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ErrorResponseProvider
{
    public function getErrorResponse(ConstraintViolationListInterface $errors): Response;
}
