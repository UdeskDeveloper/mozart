<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PostBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Mozart\Bundle\PostBundle\DependencyInjection\Compiler\PostTypesCompilerPass;

/**
 * Class MozartPostBundle
 *
 * @package Mozart\Bundle\PostBundle
 */
class MozartPostBundle extends Bundle
{

    /**
     * @param ContainerBuilder $container
     */
    public function build( ContainerBuilder $container )
    {
        parent::build( $container );
        $container->addCompilerPass( new PostTypesCompilerPass );

    }

    /**
     *
     */
    public function boot()
    {
        add_action( 'init', array( $this, 'registerPostTypes' ), 0 );
    }

    /**
     *
     */
    public function registerPostTypes()
    {

        if ( false === $this->container->has( 'mozart_post.post_type_manager' ) ) {
            return;
        }

        $postTypes = $this->container->get( 'mozart_post.post_type_manager' )
            ->getPostTypes();

        foreach ( $postTypes as $key => $postType ) {
            register_post_type( $key, $postType->getConfiguration() );
        }
    }

}