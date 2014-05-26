<?php

namespace Mozart\Bundle\NucleusBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mozart_nucleus', 'array');

        $rootNode
            ->children()
                ->arrayNode('wp')
                    ->children()
                        ->arrayNode('home')
                            ->children()
                                ->scalarNode('dir')
                                    ->defaultValue('')
                                ->end()
                                ->scalarNode('uri')
                                    ->defaultValue('')
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('site')
                            ->children()
                                ->scalarNode('uri')->defaultValue('')->end()
                            ->end()
                        ->end()

                        ->arrayNode('plugin')
                            ->children()
                                ->scalarNode('dir')->defaultValue('')->end()
                                ->scalarNode('uri')->defaultValue('')->end()
                            ->end()
                        ->end()

                        ->arrayNode('theme')
                            ->children()
                                ->scalarNode('name')->defaultValue('')->end()
                                ->scalarNode('dir')->defaultValue('')->end()
                                ->scalarNode('uri')->defaultValue('')->end()
                            ->end()
                        ->end()

                        ->arrayNode('stylesheet')
                            ->children()
                                ->scalarNode('dir')->defaultValue('')->end()
                                ->scalarNode('uri')->defaultValue('')->end()
                            ->end()
                        ->end()

                        ->arrayNode('content')
                            ->children()
                                ->scalarNode('dir')->defaultValue('')->end()
                                ->scalarNode('uri')->defaultValue('')->end()
                            ->end()
                        ->end()

                        ->arrayNode('includes')
                            ->children()
                                ->scalarNode('dir')->defaultValue('')->end()
                                ->scalarNode('uri')->defaultValue('')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ;

        return $treeBuilder;

    }
}
