<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Option\Utils;


class Option {

    public static $_parent;

    public static function isMin()
    {
        $min = '';

        if (false == self::$_parent->args['dev_mode']) {
            $min = '.min';
        }

        return $min;
    }

    /**
     * Parse CSS from output/compiler array
     *
     * @return      $css CSS string
     */
    public static function parseCSS( $cssArray = array(), $style = '', $value = '' )
    {
        // Something wrong happened
        if ( count( $cssArray ) == 0 ) {
            return;
        } else { //if ( count( $cssArray ) >= 1 ) {
            $css = '';

            foreach ($cssArray as $element => $selector) {

                // The old way
                if ($element === 0) {
                    $css = self::theOldWay( $cssArray, $style );

                    return $css;
                }

                // New way continued
                $cssStyle = $element . ':' . $value . ';';

                $css .= $selector . '{' . $cssStyle . '}';
            }
        }

        return $css;
    }

    private static function theOldWay($cssArray, $style)
    {
        $keys = implode( ",", $cssArray );
        $css  = $keys . "{" . $style . '}';

        return $css;
    }

    /**
     * initWpFilesystem - Initialized the Wordpress filesystem, if it already isn't.
     *
     * @return      void
     */
    public static function initWpFilesystem()
    {
        global $wp_filesystem;

        // Initialize the Wordpress filesystem, no more using file_put_contents function
        if ( empty( $wp_filesystem ) ) {
            require_once( ABSPATH . '/wp-admin/includes/file.php' );
            WP_Filesystem();
        }
    }

    /**
     * modRewriteCheck - Check for the installation of apache mod_rewrite
     *
     * @return      void
     */
    public static function modRewriteCheck()
    {
        if ( function_exists( 'apache_get_modules' ) ) {
            if ( ! in_array( 'mod_rewrite', apache_get_modules() ) ) {
                self::$_parent->admin_notices[] = array(
                    'type'    => 'error',
                    'msg'     => '<strong><center>The Apache mod_rewrite module is not enabled on your server.</center></strong>
                              <br/>
                              Both Wordpress and Redux require the enabling of the Apache mod_rewrite module to function properly.  Please contact whomever provides support for your server and ask them to enable the mod_rewrite module',
                    'id'      => 'mod_rewrite_notice_',
                    'dismiss' => false
                );
            }
        }
    }

    /**
     * verFromGit - Retrives latest Redux version from GIT
     *
     * @return      string $ver
     */
    private static function verFromGit()
    {
        // Get the raw framework.php from github
        $gitpage = wp_remote_get(
            'https://raw.github.com/ReduxFramework/mozart-options/master/ReduxCore/framework.php', array(
                'headers'   => array(
                    'Accept-Encoding' => ''
                ),
                'sslverify' => true,
                'timeout'   => 300
            ) );

        // Is the response code the corect one?
        if ( ! is_wp_error( $gitpage ) ) {
            if ( isset( $gitpage['body'] ) ) {
                // Get the page text.
                $body = $gitpage['body'];

                // Find version line in framework.php
                $needle = 'public static $_version =';
                $pos    = strpos( $body, $needle );

                // If it's there, continue.  We don't want errors if $pos = 0.
                if ($pos > 0) {

                    // Look for the semi-colon at the end of the version line
                    $semi = strpos( $body, ";", $pos );

                    // Error avoidance.  If the semi-colon is there, continue.
                    if ($semi > 0) {

                        // Extract the version line
                        $text = substr( $body, $pos, ( $semi - $pos ) );

                        // Find the first quote around the veersion number.
                        $quote = strpos( $body, "'", $pos );

                        // Extract the version number
                        $ver = substr( $body, $quote, ( $semi - $quote ) );

                        // Strip off quotes.
                        $ver = str_replace( "'", '', $ver );

                        return $ver;
                    }
                }
            }
        }
    }

    /**
     * updateCheck - Checks for updates to Redux Framework
     *
     * @param       string $curVer Current version of Redux Framework
     *
     * @return      void - Admin notice is diaplyed if new version is found
     */
    public static function updateCheck($curVer)
    {
        // If no cookie, check for new ver
        if ( ! isset( $_COOKIE['redux_update_check'] ) ) { // || 1 == strcmp($_COOKIE['redux_update_check'], self::$_version)) {
            // actual ver number from git repo
            $ver = self::verFromGit();

            // hour long cookie.
            setcookie( "redux_update_check", $ver, time() + 3600, '/' );
        } else {

            // saved value from cookie.  If it's different from current ver
            // we can still show the update notice.
            $ver = $_COOKIE['redux_update_check'];
        }

        // Set up admin notice on new version
        if ( 1 == strcmp( $ver, $curVer ) ) {
            self::$_parent->admin_notices[] = array(
                'type'    => 'updated',
                'msg'     => '<strong>A new build of Redux is now available!</strong><br/><br/>Your version:  <strong>' . $curVer . '</strong><br/>New version:  <strong><span style="color: red;">' . $ver . '</span></strong><br/><br/><a href="https://github.com/ReduxFramework/mozart-options">Get it now</a>&nbsp;&nbsp;|',
                'id'      => 'dev_notice_' . $ver,
                'dismiss' => true,
            );
        }
    }

    /**
     * adminNotices - Evaluates user dismiss option for displaying admin notices
     *
     * @return      void
     */
    public static function adminNotices()
    {
        global $current_user, $pagenow;

        // Check for an active admin notice array
        if ( ! empty( self::$_parent->admin_notices ) ) {

            // Enum admin notices
            foreach (self::$_parent->admin_notices as $notice) {
                if (true == $notice['dismiss']) {

                    // Get user ID
                    $userid = $current_user->ID;

                    if ( ! get_user_meta( $userid, 'ignore_' . $notice['id'] ) ) {

                        // Check if we are on admin.php.  If we are, we have
                        // to get the current page slug and tab, so we can
                        // feed it back to Wordpress.  Why>  admin.php cannot
                        // be accessed without the page parameter.  We add the
                        // tab to return the user to the last panel they were
                        // on.
                        $pageName = '';
                        $curTab   = '';
                        if ($pagenow == 'admin.php' || $pagenow == 'themes.php') {

                            // Get the current page.  To avoid errors, we'll set
                            // the redux page slug if the GET is empty.
                            $pageName = empty( $_GET['page'] ) ? '&amp;page=' . self::$_parent->args['page_slug'] : '&amp;page=' . $_GET['page'];

                            // Ditto for the current tab.
                            $curTab = empty( $_GET['tab'] ) ? '&amp;tab=0' : '&amp;tab=' . $_GET['tab'];
                        }

                        // Print the notice with the dismiss link
                        echo '<div class="' . $notice['type'] . '"><p>' . $notice['msg'] . '&nbsp;&nbsp;<a href="?dismiss=true&amp;id=' . $notice['id'] . $pageName . $curTab . '">' . __( 'Dismiss', 'mozart-options' ) . '</a>.</p></div>';
                    }
                } else {

                    // Standard notice
                    echo '<div class="' . $notice['type'] . '"><p>' . $notice['msg'] . '</a>.</p></div>';
                }
            }

            // Clear the admin notice array
            self::$_parent->admin_notices = array();
        }
    }

    /**
     * dismissAdminNotice - Updates user meta to store dismiss notice preference
     *
     * @return      void
     */
    public static function dismissAdminNotice()
    {
        global $current_user;

        // Verify the dismiss and id parameters are present.
        if ( isset( $_GET['dismiss'] ) && isset( $_GET['id'] ) ) {
            if ('true' == $_GET['dismiss'] || 'false' == $_GET['dismiss']) {

                // Get the user id
                $userid = $current_user->ID;

                // Get the notice id
                $id  = $_GET['id'];
                $val = $_GET['dismiss'];

                // Add the dismiss request to the user meta.
                update_user_meta( $userid, 'ignore_' . $id, $val );
            }
        }
    }

    public static function curlRead($filename)
    {
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $filename );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

        $data = curl_exec( $ch );

        curl_close( $ch );

        if ( empty( $data ) ) {
            $data = false;
        }

        return $data;
    }

    public static function tabFromField($parent, $field)
    {
        foreach ($parent->sections as $k => $section) {
            if ( ! isset( $section['title'] ) ) {
                continue;
            }

            if ( isset( $section['fields'] ) && ! empty( $section['fields'] ) ) {
                if ( self::recursive_array_search( $field, $section['fields'] ) ) {
                    return $k;
                    continue;
                }
            }
        }
    }

    public static function isFieldInUseByType( $fields, $field = array() )
    {
        foreach ($field as $name) {
            if ( array_key_exists( $name, $fields ) ) {
                return true;
            }
        }

        return false;
    }

    public static function isFieldInUse($parent, $field)
    {
        foreach ($parent->sections as $k => $section) {
            if ( ! isset( $section['title'] ) ) {
                continue;
            }

            if ( isset( $section['fields'] ) && ! empty( $section['fields'] ) ) {
                if ( self::recursive_array_search( $field, $section['fields'] ) ) {
                    return true;
                    continue;
                }
            }
        }
    }

    public static function isParentTheme($file)
    {
        if ( strpos( self::cleanFilePath( $file ), self::cleanFilePath( get_template_directory() ) ) !== false ) {
            return true;
        }

        return false;
    }

    public static function isChildTheme($file)
    {
        if ( strpos( self::cleanFilePath( $file ), self::cleanFilePath( get_stylesheet_directory() ) ) !== false ) {
            return true;
        }

        return false;
    }

    public static function isTheme($file)
    {
        if ( true == self::isChildTheme( $file ) || true == self::isParentTheme( $file ) ) {
            return true;
        }

        return false;
    }

    public static function array_in_array($needle, $haystack)
    {
        //Make sure $needle is an array for foreach
        if ( ! is_array( $needle ) ) {
            $needle = array( $needle );
        }
        //For each value in $needle, return TRUE if in $haystack
        foreach ($needle as $pin) { //echo 'needle' . $pin;
            if ( in_array( $pin, $haystack ) ) {
                return true;
            }
        }

        return false;
    }

    public static function recursive_array_search($needle, $haystack)
    {
        foreach ($haystack as $key => $value) {
            if ( $needle === $value || ( is_array( $value ) && self::recursive_array_search( $needle, $value ) !== false ) ) {
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
        if ( $path[ strlen( $path ) - 1 ] === '/' ) {
            $path = rtrim( $path, '/' );
        }

        return $path;
    }

    /**
     * Take a path and delete it
     *
     * @param string $path
     */
    public static function rmdir($dir)
    {
        if ( is_dir( $dir ) ) {
            $objects = scandir( $dir );
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if ( filetype( $dir . "/" . $object ) == "dir" ) {
                        rrmdir( $dir . "/" . $object );
                    } else {
                        unlink( $dir . "/" . $object );
                    }
                }
            }
            reset( $objects );
            rmdir( $dir );
        }
    }

    /**
     * Field Render Function.
     * Takes the color hex value and converts to a rgba.
     */
    public static function hex2rgba($hex, $alpha = '')
    {
        $hex = str_replace( "#", "", $hex );
        if ( strlen( $hex ) == 3 ) {
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