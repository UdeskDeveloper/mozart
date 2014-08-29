<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\ConfigBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConfigPageFieldGroupsCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition( 'mozart.config.page.fieldgroup.manager' )) {
            return;
        }

        $definition = $container->getDefinition(
            'mozart.config.page.fieldgroup.manager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'config.page.fieldgroup'
        );
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'registerFieldGroup',
                    array( new Reference( $id ) )
                );
            }
        }
    }
}
