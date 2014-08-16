<?php

namespace Mozart\Bundle\ThemeBundle;

use Mozart\Bundle\ThemeBundle\Exception\BadMethodCallException;
use Mozart\Bundle\ThemeBundle\Exception\FileNotFoundException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mozart\Bundle\ThemeBundle\DependencyInjection\Compiler\ThemesCompilerPass;

class MozartThemeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build( $container );
        $container->addCompilerPass( new ThemesCompilerPass() );
    }

    public function boot()
    {
//        if (defined( 'WP_USE_THEMES' ) && WP_USE_THEMES) {
//            add_action( 'template_redirect', array( $this->container->get( 'mozart.template.loader' ), 'load' ), 999 );
//        }
    }

}
