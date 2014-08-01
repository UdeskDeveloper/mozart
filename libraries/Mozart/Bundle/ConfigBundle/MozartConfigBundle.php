<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\ConfigBundle;

use Mozart\Bundle\ConfigBundle\Controller\OptionController;
use Mozart\Bundle\ConfigBundle\DependencyInjection\Compiler\ConfigPagesCompilerPass;
use Mozart\Bundle\ConfigBundle\DependencyInjection\Compiler\ConfigSectionsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class MozartConfigBundle
 *
 * @package Mozart\Bundle\ConfigBundle
 */
class MozartConfigBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build( $container );
        $container->addCompilerPass( new ConfigSectionsCompilerPass() );
        $container->addCompilerPass( new ConfigPagesCompilerPass() );
    }

    /**
     * Boot bundle
     */
    public function boot()
    {
        add_action( 'init', array( $this->getController(), 'initOptionManager' ) );
    }

    /**
     * @return OptionController
     */
    private function getController()
    {
        return $this->container->get( 'mozart.config.controller' );
    }
}
