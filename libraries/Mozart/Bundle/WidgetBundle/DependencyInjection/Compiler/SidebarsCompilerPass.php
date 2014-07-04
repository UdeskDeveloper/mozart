<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class SidebarsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition( 'mozart_widget.sidebar_manager' )) {
            return;
        }

        $definition = $container->getDefinition(
            'mozart_widget.sidebar_manager'
        );

        foreach ($container->findTaggedServiceIds( 'wordpress.sidebar' ) as $id => $attributes) {
            $definition->addMethodCall(
                'registerSidebar',
                array( new Reference( $id ) )
            );
        }
    }

}
