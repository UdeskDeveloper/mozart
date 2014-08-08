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
        parent::build( $container );

        // Shortcode
        $container->addCompilerPass( new ShortcodeCompilerPass() );
    }

    public function boot()
    {
        add_action( 'init', array( $this, 'onWordpressInit' ), 0 );
    }

    public function onWordpressInit()
    {
        if (!$this->container->has( 'mozart.shortcode.shortcode_chain' )) {
            return;
        }

        $shortcodes = $this->container->get( 'mozart.shortcode.shortcode_chain' )
            ->getShortcodes();

        foreach ($shortcodes as $name => $shortcode) {
            add_shortcode( $name, array( $shortcode, 'process' ) );
        }
    }

}
