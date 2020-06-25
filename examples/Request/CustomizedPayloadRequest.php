<?php

declare(strict_types=1);

namespace Fesor\RequestObject\Examples\Request;

use Fesor\RequestObject\PayloadResolver;
use Fesor\RequestObject\RequestObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;

final class CustomizedPayloadRequest extends RequestObject implements PayloadResolver
{
    public function resolvePayload(Request $request): array
    {
        $query = $request->query->all();
        // turn string to array of relations
        if (array_key_exists('includes', $query)) {
            $query['includes'] = explode(',', $query['includes']);
        }

        return $query;
    }

    public function rules()
    {
        return null;
    }
}
