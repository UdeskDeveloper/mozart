<?php

namespace Mozart\Bundle\OptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class OptionSectionsCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ( !$container->hasDefinition( 'mozart.option.sectionmanager' ) ) {
            return;
        }

        $definition = $container->getDefinition(
            'mozart.option.sectionmanager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'mozart.option.section'
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
