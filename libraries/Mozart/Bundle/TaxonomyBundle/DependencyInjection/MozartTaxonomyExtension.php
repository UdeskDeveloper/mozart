<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\TaxonomyBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/**
 * Class MozartTaxonomyExtension
 *
 * @package Mozart\Bundle\TaxonomyBundle\DependencyInjection
 */
class MozartTaxonomyExtension extends Extension
{

    /**
     * Loads the services based on your application configuration.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function load( array $configs, ContainerBuilder $container )
    {

        $loader = new YamlFileLoader( $container, new FileLocator( __DIR__ . '/../Resources/config' ) );
        $loader->load( 'services.yml' );
    }
    /**
     * @return string
     */
    public function getAlias()
    {
        return 'mozart_taxonomy';
    }
} 