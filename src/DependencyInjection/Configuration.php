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

        $root->scalarNode('api_prefix')
            ->defaultValue('')
            ->info('The api prefix for generated and handled uri\'s.');

        $root->scalarNode('logger')
            ->defaultNull()
            ->info('The service id of the psr-3 logger which should be used.');

        $root->scalarNode('psr7_factory')
            ->defaultNull()
            ->info('The service id of the http message factory which should be used.');

        $root->scalarNode('http_foundation_factory')
            ->defaultNull()
            ->info('The service id of the http foundation factory which should be used.');

        $pagination = $root->arrayNode('pagination')->addDefaultsIfNotSet()->children();
        $pagination->integerNode('limit')
            ->defaultValue(25)
            ->min(1)
            ->info('The default value for the pagination parameter "limit", 25 if not configured.');

        return $treeBuilder;
    }
}
