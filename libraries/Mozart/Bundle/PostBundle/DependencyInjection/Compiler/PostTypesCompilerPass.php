<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PostBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class PostTypesCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ( false === $container->hasDefinition( 'mozart_post.post_type_manager' ) ) {
            return;
        }

        $definition = $container->getDefinition(
            'mozart_post.post_type_manager'
        );

        foreach ( $container->findTaggedServiceIds( 'wordpress.post_type' ) as $id => $attributes ) {
            $definition->addMethodCall(
                'registerPostType',
                array( new Reference( $id ) )
            );
        }

        foreach ( $container->findTaggedServiceIds( 'wordpress.post_type.extension' ) as $id => $attributes ) {
            $definition->addMethodCall(
                'registerExtension',
                array(
                    new Reference( $id )
                )
            );
        }
    }

}
