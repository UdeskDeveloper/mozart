<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PostBundle;

use Mozart\Bundle\PostBundle\DependencyInjection\Compiler\UserCapabilitiesPerPostTypeCompilerPass;
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
    public function build(ContainerBuilder $container)
    {
        parent::build( $container );
        $container->addCompilerPass( new PostTypesCompilerPass );
        $container->addCompilerPass( new UserCapabilitiesPerPostTypeCompilerPass());

    }

    /**
     *
     */
    public function boot()
    {
    }
}
