<?php

namespace Mozart\Bundle\ShortcodeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ShortcodeCompilerPass
 *
 * @package Mozart\Bundle\ShortcodeBundle\DependencyInjection\Compiler
 */
class ShortcodeCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition( 'mozart_shortcode.shortcode_chain' )) {
            return;
        }

        $definition = $container->getDefinition(
            'mozart_shortcode.shortcode_chain'
        );

        foreach ($container->findTaggedServiceIds( 'wordpress.shortcode' ) as $id => $attributes) {
            $definition->addMethodCall(
                'addShortcode',
                array( new Reference( $id ) )
            );
        }
    }
}
