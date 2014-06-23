<?php

namespace Mozart\UI\WebIconBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class MozartWebIconBundle
 *
 * @package Mozart\UI\WebIconBundle
 */
class MozartWebIconBundle extends Bundle
{
    /**
     *
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build( ContainerBuilder $container )
    {
        parent::build( $container );
    }
}
