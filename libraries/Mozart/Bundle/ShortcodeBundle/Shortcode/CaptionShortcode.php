<?php

namespace Mozart\Bundle\ShortcodeBundle\Shortcode;

use Mozart\Bundle\ShortcodeBundle\ShortcodeInterface;

/**
 * Class CaptionShortcode
 *
 * @package Mozart\Bundle\ShortcodeBundle\Shortcode
 */
class CaptionShortcode implements ShortcodeInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'caption';
    }

    /**
     * @param      $attr
     * @param null $content
     *
     * @return null|string
     */
    public function process( $attr, $content = null )
    {
        // New-style shortcode with the caption inside the shortcode with the link and image tags.
        if (!isset( $attr['caption'] )) {
            if (preg_match( '#((?:<a [^>]+>\s*)?<img [^>]+>(?:\s*</a>)?)(.*)#is', $content, $matches )) {
                $content         = $matches[1];
                $attr['caption'] = trim( $matches[2] );
            }
        }

        extract(
            $this->shortcodeAtts(
                array(
                    'id'      => '',
                    'align'   => 'alignnone',
                    'width'   => '',
                    'caption' => ''
                ),
                $attr
            )
        );

        if (1 > (int)$width || empty( $caption )) {
            return $content;
        }

        if ($id) {
            $id = 'id="' . $id . '" ';
        }

        return '<div ' . $id . 'class="wp-caption ' . $align . '" style="width: ' . ( 10 + (int)$width ) . 'px">'
        . $content . '<p class="wp-caption-text">' . $caption . '</p></div>';
    }

    /**
     * @param $pairs
     * @param $atts
     *
     * @return array
     */
    private function shortcodeAtts( $pairs, $atts )
    {
        $atts = (array)$atts;
        $out  = array();
        foreach ($pairs as $name => $default) {
            if (array_key_exists( $name, $atts )) {
                $out[$name] = $atts[$name];
            } else {
                $out[$name] = $default;
            }
        }

        return $out;
    }
}