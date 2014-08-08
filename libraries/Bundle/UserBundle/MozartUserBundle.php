<?php

namespace Mozart\Bundle\UserBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class MozartUserBundle
 *
 * @package Mozart\Bundle\UserBundle
 */
class MozartUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build( $container );
    }

    public function boot()
    {
    }
}