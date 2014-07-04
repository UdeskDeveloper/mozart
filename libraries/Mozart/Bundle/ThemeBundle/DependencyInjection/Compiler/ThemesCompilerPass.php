<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\ThemeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ThemesCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition( 'mozart_theme.theme_chain' )) {
            return;
        }

        $definition = $container->getDefinition(
            'mozart_theme.theme_chain'
        );

        foreach ($container->findTaggedServiceIds( 'wordpress.theme' ) as $id => $attributes) {
            $definition->addMethodCall(
                'registerTheme',
                array( new Reference( $id ) )
            );
        }
    }

}
