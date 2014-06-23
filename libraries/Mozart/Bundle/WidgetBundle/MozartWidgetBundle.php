<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle;

use MyProject\Proxies\__CG__\OtherProject\Proxies\__CG__\stdClass;
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

        $widgets = $this->container->get('mozart_widget.widget_chain')->getWidgets();

        foreach ($widgets as $name => $widget) {
            $wp_widget_factory->widgets[get_class($widget)] = $widget;
            // register_widget($widgetClass);
        }
    }


} 
