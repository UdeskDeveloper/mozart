<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\OptionBundle;

use Mozart\Bundle\OptionBundle\DependencyInjection\Compiler\OptionExtensionsCompilerPass;
use Mozart\Bundle\OptionBundle\DependencyInjection\Compiler\OptionSectionsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class MozartOptionBundle
 *
 * @package Mozart\Bundle\OptionBundle
 */
class MozartOptionBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build( $container );
        $container->addCompilerPass( new OptionSectionsCompilerPass );
    }

    /**
     * Boot bundle
     */
    public function boot()
    {
        $this->container->get( 'mozart.option.controller' )->initOptionManager( );
    }
}
