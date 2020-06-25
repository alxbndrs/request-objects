<?php

declare(strict_types=1);

namespace Fesor\RequestObject;

use Symfony\Component\HttpFoundation\Request;

final class HttpPayloadResolver implements PayloadResolver
{
    public function resolvePayload(Request $request): array
    {
        if ($this->shouldNotHasRequestBody($request->getMethod())) {
            return $request->query->all();
        }

        return array_merge(
            $request->request->all(),
            $request->files->all()
        );
    }

    private function shouldNotHasRequestBody(string $methodName): bool
    {
        return in_array($methodName, ['GET', 'HEAD', 'DELETE'], true);
    }
}
