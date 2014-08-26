<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\ConfigBundle;

use Mozart\Bundle\ConfigBundle\DependencyInjection\Compiler\ConfigPageFieldGroupsCompilerPass;
use Mozart\Bundle\ConfigBundle\DependencyInjection\Compiler\ConfigPagesCompilerPass;
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
        $container->addCompilerPass( new ConfigPagesCompilerPass() );
        $container->addCompilerPass( new ConfigPageFieldGroupsCompilerPass() );
    }

    /**
     * Boot bundle
     */
    public function boot()
    {
        add_action( 'init', array( $this->container->get( 'mozart.config.page.manager' ), 'registerPages' ), 11 );
        add_action( 'init', array( $this->container->get( 'mozart.config.page.fieldgroup.manager' ), 'registerFieldGroups' ), 12 );
    }
}
