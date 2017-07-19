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
            ->info('Can be used to enable debug functionality even if environment is prod.');
        $root->scalarNode('api_prefix')->defaultValue('');
        $root->scalarNode('logger')->defaultNull();
        $root->scalarNode('psr7_factory')->defaultNull();
        $root->scalarNode('http_foundation_factory')->defaultNull();

        return $treeBuilder;
    }
}
