<?php

declare(strict_types=1);

namespace Fesor\RequestObject\Bundle;

use Fesor\RequestObject\RequestObjectBinder;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

final class ControllerEventListener
{
    private RequestObjectBinder $requestBinder;

    public function __construct(RequestObjectBinder $requestBinder)
    {
        $this->requestBinder = $requestBinder;
    }

    public function __invoke(ControllerEvent $event)
    {
        $request = $event->getRequest();
        $controller = $event->getController();

        $errorResponse = $this->requestBinder->bind($request, $controller);

        if (null === $errorResponse) {
            return;
        }

        $event->setController(function () use ($errorResponse) {
            return $errorResponse;
        });
    }
}
