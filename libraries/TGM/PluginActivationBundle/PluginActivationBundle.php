<?php

namespace TGM\PluginActivationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PluginActivationBundle extends Bundle {
    public function build(ContainerBuilder $container) {
        parent::build($container);

        if (defined('ABSPATH')) {

        }
    }
}
