<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\DependencyInjection;

use Enm\Bundle\JsonApi\Server\Controller\JsonApiController;
use Enm\Bundle\JsonApi\Server\Listener\ExceptionListener;
use Enm\JsonApi\Serializer\Deserializer;
use Enm\JsonApi\Serializer\DocumentDeserializerInterface;
use Enm\JsonApi\Serializer\DocumentSerializerInterface;
use Enm\JsonApi\Serializer\Serializer;
use Enm\JsonApi\Server\JsonApiServer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class EnmJsonApiServerExtension extends ConfigurableExtension
{
    /**
     * Configures the passed container according to the merged configuration.
     *
     * @param array $mergedConfig
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $container->autowire(Deserializer::class)->setPublic(false);
        $container->setAlias(DocumentDeserializerInterface::class, Deserializer::class)->setPublic(false);

        $container->autowire(Serializer::class)->setPublic(false);
        $container->setAlias(DocumentSerializerInterface::class, Serializer::class)->setPublic(false);

        $container->autowire(JsonApiServer::class)
            ->setPublic(false);

        $container->autowire(ExceptionListener::class)
            ->addArgument($mergedConfig['route_name_prefix'])
            ->addArgument($mergedConfig['debug'])
            ->setPublic(true)
            ->addTag('kernel.event_listener', ['event' => 'kernel.exception', 'method' => 'onKernelException']);

        $container->autowire(JsonApiController::class)
            ->addArgument($mergedConfig['url_prefix'])
            ->setPublic(true);
    }
}
