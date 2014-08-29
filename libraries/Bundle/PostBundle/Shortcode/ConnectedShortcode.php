<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PostBundle\Shortcode;

use Mozart\Bundle\ShortcodeBundle\ShortcodeInterface;
use Mozart\Component\Post\Connection\ItemList\ItemListRenderer;

class ConnectedShortcode implements ShortcodeInterface
{

    public function getName()
    {
        return 'connected';
    }

    public function process($attr)
    {
        global $post;

        $attr = shortcode_atts(
            array(
                'type' => '',
                'mode' => 'ul',
            ),
            $attr
        );

        return ItemListRenderer::query_and_render(
            array(
                'ctype'   => $attr['type'],
                'method'  => 'get_connected',
                'item'    => $post,
                'mode'    => $attr['mode'],
                'context' => 'shortcode'
            )
        );
    }
}
