<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\ShortcodeBundle\Twig\Extension;

use Mozart\Bundle\ShortcodeBundle\ShortcodeChain;

/**
 * Class ShortcodeExtension
 *
 * @package Mozart\Bundle\ShortcodeBundle\Twig\Extension
 */
class ShortcodeExtension extends \Twig_Extension
{
    /**
     * @var ShortcodeChain
     */
    protected $shortcodeChain;

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'mozart_shortcode';
    }

    /**
     * @param ShortcodeChain $shortcodeChain
     */
    public function __construct(ShortcodeChain $shortcodeChain)
    {
        $this->shortcodeChain = $shortcodeChain;
    }

    /**
     * @param string $content Content to search for shortcodes
     *
     * @return string Content with shortcodes filtered out.
     */
    public function doShortcode($content)
    {
        // replicates do_shortcode() from Wordpress
        return $this->shortcodeChain->process( $content );
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter( 'do_shortcode', array( $this, 'doShortcode' ) ),
            new \Twig_SimpleFilter( 'wp_shortcode', array( $this, 'doShortcode' ) ),
            new \Twig_SimpleFilter( 'shortcode', array( $this, 'doShortcode' ) ),
        );
    }
}
