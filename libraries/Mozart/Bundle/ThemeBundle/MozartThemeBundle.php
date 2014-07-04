<?php

namespace Mozart\Bundle\ThemeBundle;

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
    }

}
