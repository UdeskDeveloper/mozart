<?php

namespace Mozart\Bundle\BackofficeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class OptionSectionsCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('mozart_backoffice.options.sectionmanager')) {
            return;
        }

        $definition = $container->getDefinition(
                'mozart_backoffice.options.sectionmanager'
        );

        $taggedServices = $container->findTaggedServiceIds(
                'mozart_backoffice.options.section'
        );
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addSection',
                    array(new Reference($id), $attributes["alias"])
                );
            }
        }
    }

}
