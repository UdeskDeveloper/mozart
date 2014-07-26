<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle;

use Mozart\Bundle\WidgetBundle\DependencyInjection\Compiler\SidebarsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Mozart\Bundle\WidgetBundle\DependencyInjection\Compiler\WidgetsCompilerPass;

/**
 * Class MozartWidgetBundle
 *
 * @package Mozart\Bundle\WidgetBundle
 */
class MozartWidgetBundle extends Bundle
{

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build( $container );
        $container->addCompilerPass( new WidgetsCompilerPass() );
        $container->addCompilerPass( new SidebarsCompilerPass() );
    }

    /**
     *
     */
    public function boot()
    {
        add_action( 'widgets_init', array( $this, 'registerSidebars' ), 0 );
        add_action( 'widgets_init', array( $this, 'registerWidgets' ), 0 );
    }

    public function registerSidebars()
    {
        if (false === $GLOBALS['wp_registered_sidebars'] || false === $this->container->has(
                'mozart.widget.sidebar_manager'
            )
        ) {
            return false;
        }

        add_theme_support( 'widgets' );

        $sidebars = $this->container->get( 'mozart.widget.sidebar_manager' )->getSidebars();

        foreach ($sidebars as $sidebar) {

            $GLOBALS['wp_registered_sidebars'][$sidebar->getKey()] = $sidebar->getConfiguration();

            do_action( 'register_sidebar', $sidebar->getConfiguration() );
        }
    }

    /**
     *
     */
    public function registerWidgets()
    {

        if (false === isset( $GLOBALS['wp_widget_factory'] ) || false === $this->container->has(
                'mozart.widget.widget_manager'
            )
        ) {
            return false;
        }

        $widgets = $this->container->get( 'mozart.widget.widget_manager' )->getWidgets();

        $GLOBALS['wp_widget_factory']->widgets = array_merge( $GLOBALS['wp_widget_factory']->widgets, $widgets );
    }
}
