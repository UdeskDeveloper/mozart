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

    public function boot()
    {
    }

}
