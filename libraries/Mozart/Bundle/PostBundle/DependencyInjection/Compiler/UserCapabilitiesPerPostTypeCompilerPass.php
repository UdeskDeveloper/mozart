<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PostBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class UserCapabilitiesPerPostTypeCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process( ContainerBuilder $container )
    {
        if (false === $container->hasDefinition( 'mozart_post.post_type_manager' )) {
            return;
        }

        $postTypes = $container->get( 'mozart_post.post_type_manager' )->getPostTypes();

        foreach ($postTypes as $postType) {
            // TODO: add the capabilities
        }
    }
}
