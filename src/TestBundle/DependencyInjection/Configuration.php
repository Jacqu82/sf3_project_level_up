<?php

namespace TestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('test_root');

        $rootNode
            ->children()
                ->booleanNode('boolean_child')
                    ->defaultTrue()
                ->end()
            ->end()
        ;


        return $treeBuilder;
    }
}
