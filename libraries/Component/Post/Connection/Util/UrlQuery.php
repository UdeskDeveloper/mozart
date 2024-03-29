<?php
namespace Mozart\Component\Post\Connection\Util;

class UrlQuery
{
    public static function get_custom_qv()
    {
        return array( 'connected_type', 'connected_items', 'connected_direction' );
    }

    public static function init()
    {
        add_filter( 'query_vars', array( __CLASS__, 'query_vars' ) );
    }

    // Make the query vars public
    public static function query_vars($public_qv)
    {
        return array_merge( $public_qv, self::get_custom_qv() );
    }
}
