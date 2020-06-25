<?php

declare(strict_types=1);

namespace Fesor\RequestObject\Bundle;

use Fesor\RequestObject\Bundle\DependeyInjection\RequestObjectExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class RequestObjectBundle extends Bundle
{
    protected function getContainerExtensionClass(): string
    {
        return RequestObjectExtension::class;
    }
}
