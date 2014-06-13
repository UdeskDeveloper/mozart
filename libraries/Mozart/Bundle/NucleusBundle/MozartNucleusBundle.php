<?php

namespace Mozart\Bundle\NucleusBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MozartNucleusBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public function boot()
    {
        if (\Mozart::isWpRunning() === false) {
            return;
        }

        add_filter('template_include', array($this, 'templateInclude'), 99);
        add_filter('404_template', array($this, 'templateInclude'), 99);
        add_action('widgets_init', array($this, 'registerWidgets'));
    }

    public function registerWidgets()
    {
        register_widget('\Mozart\Bundle\NucleusBundle\Widget\CallToAction');
    }

    public function templateInclude($template)
    {
//        $theme_name = strtolower((string) wp_get_theme());
//        $template = str_replace('/' . $theme_name . '/', '/' . $theme_name . '/templates/', $template);
//        echo $template;
        return $template;
    }

}
