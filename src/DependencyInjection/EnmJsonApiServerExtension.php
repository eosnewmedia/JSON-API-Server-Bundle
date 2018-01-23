<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\DependencyInjection;

use Enm\Bundle\JsonApi\Server\Controller\JsonApiController;
use Enm\Bundle\JsonApi\Server\JsonApiServerDecorator;
use Enm\Bundle\JsonApi\Server\Listener\ExceptionListener;
use Enm\Bundle\JsonApi\Server\Routing\JsonApiLoader;
use Enm\JsonApi\JsonApiInterface;
use Enm\JsonApi\Server\JsonApiServer;
use Enm\JsonApi\Server\Pagination\OffsetPaginationLinkGenerator;
use Enm\JsonApi\Server\Pagination\PaginationLinkGeneratorInterface;
use Enm\JsonApi\Server\RequestHandler\RequestHandlerChain;
use Enm\JsonApi\Server\RequestHandler\RequestHandlerInterface;
use Enm\JsonApi\Server\RequestHandler\RequestHandlerRegistry;
use Enm\JsonApi\Server\RequestHandler\ResourceProviderRequestHandler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
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
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $container->setParameter(
            'enm.json_api_server.api_prefix',
            (string)$mergedConfig['api_prefix']
        );
        $container->setParameter(
            'enm.json_api_server.pagination.limit',
            (int)$mergedConfig['pagination']['limit']
        );

        $this->configureRequestHandlers($container);
        $this->configureServer($container, $mergedConfig);

        $container->register(JsonApiLoader::class)
            ->setPublic(true)
            ->addArgument((string)$mergedConfig['api_prefix'])
            ->addTag('routing.loader');

        $container->register(ExceptionListener::class)
            ->setPublic(true)
            ->addArgument(new Reference(JsonApiServerDecorator::class))
            ->addTag('kernel.event_listener', ['event' => 'kernel.exception', 'method' => 'onKernelException']);

        $container->register(JsonApiController::class)
            ->setPublic(true)
            ->addArgument(new Reference(JsonApiServerDecorator::class));

        $container->register(OffsetPaginationLinkGenerator::class)
            ->setPublic(false)
            ->addArgument((int)$mergedConfig['pagination']['limit']);
        $container->setAlias('enm.json_api_server.pagination.offset_based', OffsetPaginationLinkGenerator::class);
        $container->setAlias(PaginationLinkGeneratorInterface::class, OffsetPaginationLinkGenerator::class);
    }

    /**
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    protected function configureRequestHandlers(ContainerBuilder $container)
    {
        $container->register(RequestHandlerRegistry::class)->setPublic(false);
        //@deprecated service id will be removed in 3.0
        $container->setAlias(
            'enm.json_api_server.request_handler.registry',
            RequestHandlerRegistry::class
        );

        $container->register(ResourceProviderRequestHandler::class)->setPublic(false);
        //@deprecated service id will be removed in 3.0
        $container->setAlias(
            'enm.json_api_server.request_handler.resource_provider',
            ResourceProviderRequestHandler::class
        );

        $container->register(RequestHandlerChain::class)
            ->setPublic(false)
            ->addMethodCall('addRequestHandler', [new Reference(RequestHandlerRegistry::class)])
            ->addMethodCall('addRequestHandler', [new Reference(ResourceProviderRequestHandler::class)]);
        $container->setAlias(RequestHandlerInterface::class, RequestHandlerChain::class);
        //@deprecated service id will be removed in 3.0
        $container->setAlias('enm.json_api_server.request_handler.chain', RequestHandlerChain::class);
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     * @throws \Exception
     */
    protected function configureServer(ContainerBuilder $container, array $config)
    {
        $container->register(JsonApiServer::class)
            ->setPublic(false)
            ->addArgument(new Reference(RequestHandlerInterface::class))
            ->addArgument((string)$config['api_prefix']);
        $container->setAlias(JsonApiInterface::class, JsonApiServer::class);
        //@deprecated service id will be removed in 3.0
        $container->setAlias('enm.json_api_server.server', JsonApiServer::class);


        $container->register(JsonApiServerDecorator::class)
            ->setPublic(false)
            ->addArgument(new Reference(JsonApiServer::class))
            ->addArgument(\in_array($container->getParameter('kernel.environment'), ['dev', 'test'], true));
        //@deprecated service id will be removed in 3.0
        $container->setAlias('enm.json_api_server.server_decorator', JsonApiServerDecorator::class);

        if ($config['debug']) {
            $container->getDefinition(JsonApiServerDecorator::class)
                ->addMethodCall(
                    'setDebug',
                    [true]
                );
        }

        if ($config['logger'] !== null) {
            $container->getDefinition(JsonApiServer::class)
                ->addMethodCall(
                    'setLogger',
                    [
                        new Reference($config['logger'])
                    ]
                );
        }

        if ($config['psr7_factory'] !== null) {
            $container->getDefinition(JsonApiServerDecorator::class)
                ->addMethodCall(
                    'setPsr7Factory',
                    [
                        new Reference($config['psr7_factory'])
                    ]
                );
        }

        if ($config['http_foundation_factory'] !== null) {
            $container->getDefinition(JsonApiServerDecorator::class)
                ->addMethodCall(
                    'setHttpFoundationFactory',
                    [
                        new Reference($config['http_foundation_factory'])
                    ]
                );
        }
    }
}
