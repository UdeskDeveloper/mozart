<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Mozart\Bundle\WidgetBundle\DependencyInjection\Compiler\WidgetsCompilerPass;

/**
 * Class MozartWidgetBundle
 * @package Mozart\Bundle\WidgetBundle
 */
class MozartWidgetBundle extends Bundle
{

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new WidgetsCompilerPass());
    }

    /**
     *
     */
    public function boot()
    {
        add_action('widgets_init', array($this, 'registerWidgets'), 0);
    }

    /**
     *
     */
    public function registerWidgets()
    {

        if (false === $this->container->has('mozart_widget.widget_chain')) {
            return;
        }
        global $wp_widget_factory;

        $widgets = (array)$this->container->get('mozart_widget.widget_chain')->getWidgets();

        $wp_widget_factory->widgets = array_merge($wp_widget_factory->widgets, $widgets);
    }
} 
