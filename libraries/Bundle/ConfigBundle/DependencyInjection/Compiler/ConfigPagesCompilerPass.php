<?php

namespace Mozart\Bundle\ConfigBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ConfigPagesCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition( 'mozart.config.page.manager' )) {
            return;
        }

        $definition = $container->getDefinition(
            'mozart.config.page.manager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'config.page'
        );
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'registerPage',
                    array( new Reference( $id ) )
                );
            }
        }
    }
}
