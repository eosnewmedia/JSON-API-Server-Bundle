<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     * @throws \Exception
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('enm_json_api_server')->children();
        $root->booleanNode('debug')
            ->defaultFalse()
            ->info('Can be used to enable debug functionality (extended json api errors).');

        $root->scalarNode('url_prefix')
            ->defaultNull()
            ->info('The api url prefix for generated and handled uri\'s.');

        $root->scalarNode('route_name_prefix')
            ->defaultValue('enm.json_api')
            ->info('The route name prefix for the symfony routes');

        return $treeBuilder;
    }
}
