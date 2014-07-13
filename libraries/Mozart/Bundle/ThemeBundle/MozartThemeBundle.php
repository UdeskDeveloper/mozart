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
    public function __construct()
    {
        add_filter(
            'register_mozart_bundle',
            function ($bundles) {
                if (false === file_exists( get_template_directory() . '/vendor/autoload.php' )) {
                    throw new FileNotFoundException( '/vendor/autoload.php was not found in your theme' );
                }
                include_once get_template_directory() . '/vendor/autoload.php';

                $currentThemeName = wp_get_theme()->get( 'Name' );
                $currentThemeAuthor = wp_get_theme()->get( 'Author' );

                $vendor = Container::camelize( $currentThemeAuthor );
                $bundle = Container::camelize( $currentThemeName );

                $class = "\\$vendor\\{$bundle}Bundle\\{$vendor}{$bundle}Bundle";

                if (false === class_exists( $class )) {
                    throw new BadMethodCallException(
                        'The current theme does not follow the naming
                    convention for the associated bundle class;
                    you must define the class ' . $class
                    );
                }

                $bundles[] = new $class();

                return $bundles;
            }
        );
    }

    public function build(ContainerBuilder $container)
    {
        parent::build( $container );
        $container->addCompilerPass( new ThemesCompilerPass() );
    }

    public function boot()
    {
    }

}
