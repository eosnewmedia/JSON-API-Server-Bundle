<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
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

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.xml');

        if ($mergedConfig['debug']) {
            $container->getDefinition('enm.json_api_server.server_decorator')
                ->addMethodCall(
                    'setDebug',
                    [true]
                );
        }

        if ($mergedConfig['logger'] !== null) {
            $container->getDefinition('enm.json_api_server.server')
                ->addMethodCall(
                    'setLogger',
                    [
                        new Reference($mergedConfig['logger'])
                    ]
                );
        }

        if ($mergedConfig['psr7_factory'] !== null) {
            $container->getDefinition('enm.json_api_server.server_decorator')
                ->addMethodCall(
                    'setPsr7Factory',
                    [
                        new Reference($mergedConfig['psr7_factory'])
                    ]
                );
        }

        if ($mergedConfig['http_foundation_factory'] !== null) {
            $container->getDefinition('enm.json_api_server.server_decorator')
                ->addMethodCall(
                    'setHttpFoundationFactory',
                    [
                        new Reference($mergedConfig['http_foundation_factory'])
                    ]
                );
        }
    }
}
