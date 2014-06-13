<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\ShortcodeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mozart\Bundle\ShortcodeBundle\DependencyInjection\Compiler\ShortcodeCompilerPass;


class MozartShortcodeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // Shortcode
        $container->addCompilerPass(new ShortcodeCompilerPass());
    }

} 