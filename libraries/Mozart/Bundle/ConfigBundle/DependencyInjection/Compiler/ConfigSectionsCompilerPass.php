<?php

namespace Mozart\Bundle\ConfigBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ConfigSectionsCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ( !$container->hasDefinition( 'mozart.config.sectionmanager' ) ) {
            return;
        }

        $definition = $container->getDefinition(
            'mozart.config.sectionmanager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'mozart.config.section'
        );
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addSection',
                    array( new Reference( $id ), $attributes["alias"] )
                );
            }
        }

//        $container->setParameter( 'settings', get_option( 'mozart-options', array() ) );
    }

}
