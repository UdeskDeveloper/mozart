<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PluginBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
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
        add_action( 'init', array( $this->container->get('mozart.plugin.manager'), 'init' ) );
    }

}
