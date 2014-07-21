<?php

namespace Mozart\Bundle\OptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class OptionExtensionsCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition( 'mozart.option.extensionmanager' )) {
            return;
        }

        $definition = $container->getDefinition(
            'mozart.option.extensionmanager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'mozart.option.extension'
        );
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addExtension',
                    array( new Reference( $id ) )
                );
            }
        }
    }

}
