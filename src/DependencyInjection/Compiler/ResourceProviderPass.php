<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\DependencyInjection\Compiler;

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
        if (!$container->hasDefinition('enm.json_api.resource_provider')) {
            return;
        }

        $definition = $container->getDefinition('enm.json_api.resource_provider');
        $providers = $container->findTaggedServiceIds('json_api.resource_provider');

        /**
         * @var string $id
         * @var array $tags
         */
        foreach ($providers as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'addProvider',
                    [
                        new Reference($id),
                        $attributes['type']
                    ]
                );
            }
        }
    }
}
