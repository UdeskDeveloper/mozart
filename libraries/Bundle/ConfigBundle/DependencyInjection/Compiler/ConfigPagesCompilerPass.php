<?php

namespace Mozart\Bundle\ConfigBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ConfigPagesCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ( !$container->hasDefinition( 'mozart.config.pagemanager' ) ) {
            return;
        }

        $definition = $container->getDefinition(
            'mozart.config.pagemanager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'config.page'
        );
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                if (!isset($attributes["alias"])) {
                    $attributes["alias"] = '';
                }
                $definition->addMethodCall(
                    'addPage',
                    array( new Reference( $id ), $attributes["alias"] )
                );
            }
        }
    }
}
