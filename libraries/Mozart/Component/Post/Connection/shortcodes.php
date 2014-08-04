<?php
namespace Mozart\Component\Post\Connection;

class shortcodes
{
    public static function init()
    {
        add_shortcode( 'p2p_connected', array( __CLASS__, 'connected' ) );
        add_shortcode( 'p2p_related', array( __CLASS__, 'related' ) );
    }

    public static function connected($attr)
    {
        return self::get_list( $attr, 'get_connected' );
    }

    public static function related($attr)
    {
        return self::get_list( $attr, 'get_related' );
    }

    private static function get_list($attr, $method)
    {
        global $post;

        $attr = shortcode_atts( array(
            'type' => '',
            'mode' => 'ul',
        ), $attr );

        return P2P_List_Renderer::query_and_render( array(
            'ctype' => $attr['type'],
            'method' => $method,
            'item' => $post,
            'mode' => $attr['mode'],
            'context' => 'shortcode'
        ) );
    }
}
