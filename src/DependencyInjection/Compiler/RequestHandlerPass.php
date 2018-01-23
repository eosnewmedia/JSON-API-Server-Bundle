<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\DependencyInjection\Compiler;

use Enm\JsonApi\Server\RequestHandler\RequestHandlerChain;
use Enm\JsonApi\Server\RequestHandler\RequestHandlerRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class RequestHandlerPass implements CompilerPassInterface
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
        if (!$container->hasDefinition(RequestHandlerRegistry::class)) {
            return;
        }

        if (!$container->hasDefinition(RequestHandlerChain::class)) {
            return;
        }

        $registry = $container->getDefinition(RequestHandlerRegistry::class);
        $chain = $container->getDefinition(RequestHandlerChain::class);

        $handlers = $container->findTaggedServiceIds('json_api_server.request_handler');

        /**
         * @var string $id
         * @var array $tags
         */
        foreach ($handlers as $id => $tags) {
            foreach ($tags as $attributes) {
                if (array_key_exists('type', $attributes)) {
                    $registry->addMethodCall(
                        'addRequestHandler',
                        [
                            (string)$attributes['type'],
                            new Reference($id)
                        ]
                    );
                } else {
                    $chain->addMethodCall(
                        'addRequestHandler',
                        [
                            new Reference($id)
                        ]
                    );
                }
            }
        }
    }
}
