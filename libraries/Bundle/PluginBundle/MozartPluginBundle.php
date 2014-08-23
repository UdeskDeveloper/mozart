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

        add_filter(
            PluginEvents::BEFORE_ACTIVATION,
            function ($plugin, $network_deactivating) {
                $event = new \Mozart\Bundle\PluginBundle\Event\PluginEvent();
                $event->setBaseName( $plugin );
                $event->setNetworkDeactivating( $network_deactivating );

                \Mozart::dispatch( PluginEvents::BEFORE_ACTIVATION, $event );
            },
            10,
            2
        );
        add_filter(
            PluginEvents::AFTER_ACTIVATION,
            function ($plugin, $network_deactivating) {
                $event = new \Mozart\Bundle\PluginBundle\Event\PluginEvent();
                $event->setBaseName( $plugin );
                $event->setNetworkDeactivating( $network_deactivating );

                \Mozart::dispatch( PluginEvents::AFTER_ACTIVATION, $event );
            },
            10,
            2
        );
        add_filter(
            PluginEvents::BEFORE_DEACTIVATION,
            function ($plugin, $network_deactivating) {
                $event = new \Mozart\Bundle\PluginBundle\Event\PluginEvent();
                $event->setBaseName( $plugin );
                $event->setNetworkDeactivating( $network_deactivating );

                \Mozart::dispatch( PluginEvents::BEFORE_DEACTIVATION, $event );
            },
            10,
            2
        );
        add_filter(
            PluginEvents::AFTER_DEACTIVATION,
            function ($plugin, $network_deactivating) {
                $event = new \Mozart\Bundle\PluginBundle\Event\PluginEvent();
                $event->setBaseName( $plugin );
                $event->setNetworkDeactivating( $network_deactivating );

                \Mozart::dispatch( PluginEvents::AFTER_DEACTIVATION, $event );
            },
            10,
            2
        );
    }
}
