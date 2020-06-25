<?php

declare(strict_types=1);

namespace Fesor\RequestObject\Bundle\DependeyInjection;

use Fesor\RequestObject\Bundle\ControllerEventListener;
use Fesor\RequestObject\HttpPayloadResolver;
use Fesor\RequestObject\PayloadResolver;
use Fesor\RequestObject\RequestObjectBinder;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

final class RequestObjectExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->registerPayloadResolver($container);
        $this->registerRequestBinder($container);
        $this->registerEventListener($container);
    }

    private function registerPayloadResolver(ContainerBuilder $container): void
    {
        $container->setDefinition(HttpPayloadResolver::class, new Definition(HttpPayloadResolver::class));

        $container->setAlias(PayloadResolver::class, HttpPayloadResolver::class);
        $container->setAlias('request_object.payload_resolver', PayloadResolver::class);
        $container->setAlias('request_object.payload_resolver.http', HttpPayloadResolver::class);
    }

    private function registerRequestBinder(ContainerBuilder $container): void
    {
        $definition = new Definition(RequestObjectBinder::class, []);
        $definition->setAutowired(true);
        $definition->setPublic(false);
        $container->setDefinition(RequestObjectBinder::class, $definition);
        $container->setAlias('request_object.request_binder', RequestObjectBinder::class);
    }

    private function registerEventListener(ContainerBuilder $container): void
    {
        $definition = new Definition(ControllerEventListener::class, [
            new Reference(RequestObjectBinder::class),
        ]);
        $definition->addTag('kernel.event_listener', array(
            'event' => 'kernel.controller',
        ));

        $container->setDefinition(ControllerEventListener::class, $definition);
        $container->setAlias('request_object.event_listener.controller', ControllerEventListener::class);
    }
}
