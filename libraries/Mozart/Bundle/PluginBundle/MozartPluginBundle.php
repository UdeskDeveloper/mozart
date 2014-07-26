<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PluginBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class MozartPluginBundle
 *
 * @package Mozart\Bundle\PluginBundle
 */
class MozartPluginBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build( $container );
    }

    public function boot()
    {
        add_action( 'init', array( $this->container->get( 'mozart.plugin.manager' ), 'init' ) );
        add_action( 'deactivated_plugin', array( $this, 'clearCache' ), 10, 2 );
        add_action( 'activated_plugin', array( $this, 'clearCache' ), 10, 2 );
    }

    public function clearCache($plugin, $network_activation)
    {
        $filesystem = new Filesystem();

        if ($filesystem->exists( $this->container->getParameter( 'kernel.cache_dir' ) )) {
            $filesystem->remove( $this->container->getParameter( 'kernel.cache_dir' ) );
        }
    }

}
