<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\DependencyInjection\Compiler;

use Enm\JsonApi\Server\JsonApiServer;
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
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(JsonApiServer::class)) {
            return;
        }

        $server = $container->getDefinition(JsonApiServer::class);
        $handlers = $container->findTaggedServiceIds('json_api_server.request_handler');

        /**
         * @var string $id
         * @var array $tags
         */
        foreach ($handlers as $id => $tags) {
            foreach ($tags as $attributes) {
                $server->addMethodCall('addHandler', [(string)$attributes['type'], new Reference($id)]);
            }
        }
    }
}
