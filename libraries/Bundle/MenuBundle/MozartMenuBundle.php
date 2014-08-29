<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\MenuBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MozartMenuBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build( $container );
    }

    public function boot()
    {

    }

}
