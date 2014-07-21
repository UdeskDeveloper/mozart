<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Option\Utils;

class OptionUtil
{
    /**
     * Parse CSS from output/compiler array
     *
     * @return $css CSS string
     */
    public static function parseCSS( $cssArray = array(), $style = '', $value = '' )
    {
        $css = '';

        if (count( $cssArray ) == 0) {
            return $css;
        } else {

            $keys = implode( ",", $cssArray );

            foreach ($cssArray as $element => $selector) {

                // The old way
                if ($element === 0) {
                    return $keys . "{" . $style . '}';
                }

                // New way continued
                $cssStyle = $element . ':' . $value . ';';

                $css .= $selector . '{' . $cssStyle . '}';
            }
        }

        return $css;
    }

    /**
     * initWpFilesystem - Initialized the Wordpress filesystem, if it already isn't.
     *
     * @return void
     */
    public static function initWpFilesystem()
    {
        global $wp_filesystem;

        // Initialize the Wordpress filesystem, no more using file_put_contents function
        if (empty( $wp_filesystem )) {
            require_once( ABSPATH . '/wp-admin/includes/file.php' );
            WP_Filesystem();
        }
    }

    public function curlRead($filename)
    {
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $filename );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

        $data = curl_exec( $ch );

        curl_close( $ch );

        if (empty( $data )) {
            $data = false;
        }

        return $data;
    }

    public static function isFieldInUseByType( $fields, $field = array() )
    {
        foreach ($field as $name) {
            if (array_key_exists( $name, $fields )) {
                return true;
            }
        }

        return false;
    }

    public static function isFieldInUse($sections, $field)
    {
        foreach ($sections as $k => $section) {
            if (!isset( $section['title'] )) {
                continue;
            }

            if (isset( $section['fields'] ) && !empty( $section['fields'] )) {
                if (self::recursiveArraySearch( $field, $section['fields'] )) {
                    return true;
                    continue;
                }
            }
        }
    }

    public static function array_in_array($needle, $haystack)
    {
        //Make sure $needle is an array for foreach
        if (!is_array( $needle )) {
            $needle = array( $needle );
        }
        //For each value in $needle, return TRUE if in $haystack
        foreach ($needle as $pin) { //echo 'needle' . $pin;
            if (in_array( $pin, $haystack )) {
                return true;
            }
        }

        return false;
    }

    public static function recursiveArraySearch($needle, $haystack)
    {
        foreach ($haystack as $key => $value) {
            if ($needle === $value || ( is_array( $value ) && self::recursiveArraySearch(
                        $needle,
                        $value
                    ) !== false )
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Take a path and return it clean
     *
     * @param string $path
     */
    public static function cleanFilePath($path)
    {
        $path = str_replace( '', '', str_replace( array( "\\", "\\\\" ), '/', $path ) );
        if ($path[strlen( $path ) - 1] === '/') {
            $path = rtrim( $path, '/' );
        }

        return $path;
    }

    /**
     * Field Render Function.
     * Takes the color hex value and converts to a rgba.
     */
    public static function hex2rgba($hex, $alpha = '')
    {
        $hex = str_replace( "#", "", $hex );
        if (strlen( $hex ) == 3) {
            $r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
            $g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
            $b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
        } else {
            $r = hexdec( substr( $hex, 0, 2 ) );
            $g = hexdec( substr( $hex, 2, 2 ) );
            $b = hexdec( substr( $hex, 4, 2 ) );
        }
        $rgb = $r . ',' . $g . ',' . $b;

        if ('' == $alpha) {
            return $rgb;
        } else {
            $alpha = floatval( $alpha );

            return 'rgba(' . $rgb . ',' . $alpha . ')';
        }
    }
}
