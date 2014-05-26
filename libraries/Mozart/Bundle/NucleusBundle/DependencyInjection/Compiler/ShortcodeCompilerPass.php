<?php

namespace Mozart\Bundle\NucleusBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ShortcodeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('mozart_nucleus.shortcode_chain')) {
            return;
        }

        $definition = $container->getDefinition(
            'mozart_nucleus.shortcode_chain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'mozart_nucleus.shortcode'
        );

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addShortcode',
                array(new Reference($id))
            );
        }
    }
}
