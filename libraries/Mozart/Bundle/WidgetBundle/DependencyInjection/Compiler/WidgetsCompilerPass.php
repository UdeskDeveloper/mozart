<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class WidgetsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition( 'mozart.widget.widget_manager' )) {
            return;
        }

        $definition = $container->getDefinition(
            'mozart.widget.widget_manager'
        );

        foreach ($container->findTaggedServiceIds( 'wordpress.widget' ) as $id => $attributes) {
            $definition->addMethodCall(
                'registerWidget',
                array( new Reference( $id ) )
            );
        }
    }

}
