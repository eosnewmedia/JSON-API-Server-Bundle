<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class ResourceProviderPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('enm.json_api_server.request_handler.resource_provider')) {
            return;
        }

        $handler = $container->getDefinition('enm.json_api_server.request_handler.resource_provider');
        $resourceProviders = $container->findTaggedServiceIds('json_api_server.resource_provider');

        /**
         * @var string $id
         * @var array $tags
         */
        foreach ($resourceProviders as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!array_key_exists('type', $attributes)) {
                    throw new InvalidDefinitionException('Missing "type" for a resource provider!');
                }
                $handler->addMethodCall(
                    'addResourceProvider',
                    [
                        $attributes['type'],
                        new Reference($id)
                    ]
                );
            }
        }
    }
}
