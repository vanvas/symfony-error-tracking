<?php
declare(strict_types=1);

namespace Vim\ErrorTracking\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('error_tracking');

        $treeBuilder
            ->getRootNode()
            ->children()
                ->arrayNode('ignored_exceptions')->scalarPrototype()->end()->end()
                ->arrayNode('ignored_codes')->scalarPrototype()->end()->end()
                ->arrayNode('ignored_levels')->scalarPrototype()->end()->end()
                ->arrayNode('ignored_messages')->scalarPrototype()->end()->end()
                ->scalarNode('url')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
