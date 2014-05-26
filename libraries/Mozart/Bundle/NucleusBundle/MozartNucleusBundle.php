<?php

namespace Mozart\Bundle\NucleusBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mozart\Bundle\NucleusBundle\Shortcode\ShortcodeManager;

use Mozart\Bundle\NucleusBundle\DependencyInjection\Compiler\ShortcodeCompilerPass;

class MozartNucleusBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // Shortcode
        $container->addCompilerPass(new ShortcodeCompilerPass());
    }

    public function boot()
    {
        if (\Mozart::isWpRunning() === false) {
            return;
        }

        new ShortcodeManager();
        add_filter('template_include', array($this, 'templateInclude'), 99);
        add_filter('404_template', array($this, 'templateInclude'), 99);
        add_action('widgets_init', array($this, 'registerWidgets'));
        add_action('init', array($this, 'addShortcodes'));
    }

    public function addShortcodes()
    {
        add_shortcode('content_box', array('\Mozart\Bundle\NucleusBundle\Shortcode\Shortcodes\ContentBox', 'shortcode'));

        add_shortcode('row', array('\Mozart\Bundle\NucleusBundle\Shortcode\Shortcodes\Columns', 'row'));
        add_shortcode('span3', array('\Mozart\Bundle\NucleusBundle\Shortcode\Shortcodes\Columns', 'row_span3'));
        add_shortcode('span4', array('\Mozart\Bundle\NucleusBundle\Shortcode\Shortcodes\Columns', 'row_span4'));
        add_shortcode('span6', array('\Mozart\Bundle\NucleusBundle\Shortcode\Shortcodes\Columns', 'row_span6'));
        add_shortcode('span8', array('\Mozart\Bundle\NucleusBundle\Shortcode\Shortcodes\Columns', 'row_span8'));
        add_shortcode('span9', array('\Mozart\Bundle\NucleusBundle\Shortcode\Shortcodes\Columns', 'row_span9'));
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
