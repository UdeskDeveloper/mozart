<?php

namespace Mozart\Bundle\OptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ReduxSectionsCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ( !$container->hasDefinition( 'redux.sectionmanager' ) ) {
            return;
        }

        $definition = $container->getDefinition(
            'redux.sectionmanager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'redux.section'
        );
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addSection',
                    array( new Reference( $id ), $attributes["alias"] )
                );
            }
        }

        $container->setParameter( 'settings', get_option( 'mozart-options', array() ) );
    }

}
