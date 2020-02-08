<?php

namespace TestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class TestExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $tree = $configuration->getConfigTreeBuilder();

//        echo '<pre>';
//        var_dump($config);
//        echo '</pre>';
//        die;
//        dump($configs);die;
    }
}
