<?php
/**
 * Plugin installation and activation for WordPress themes.
 *
 * @package   TGM-Plugin-Activation
 * @version   2.3.6
 * @author    Thomas Griffin <thomas@thomasgriffinmedia.com>
 * @author    Gary Jones <gamajo@gamajo.com>
 * @copyright Copyright (c) 2012, Thomas Griffin
 * @license   http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link      https://github.com/thomasgriffin/TGM-Plugin-Activation
 */

/*
    Copyright 2012  Thomas Griffin  (email : thomas@thomasgriffinmedia.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/** Create a new instance of the class */
new TGM_Plugin_Activation;

if ( ! function_exists( 'tgmpa' ) ) {
    /**
     * Helper function to register a collection of required plugins.
     *
     * @since 2.0.0
     * @api
     *
     * @param array $plugins An array of plugin arrays
     * @param array $config  Optional. An array of configuration values
     */
    function tgmpa( $plugins, $config = array() )
    {
        foreach ( $plugins as $plugin )
            TGM_Plugin_Activation::$instance->register( $plugin );

        if ( $config )
            TGM_Plugin_Activation::$instance->config( $config );

    }
}


/**
 * The WP_Upgrader file isn't always available. If it isn't available,
 * we load it here.
 *
 * We check to make sure no action or activation keys are set so that WordPress
 * doesn't try to re-include the class when processing upgrades or installs outside
 * of the class.
 *
 * @since 2.2.0
 */
if ( isset( $_GET[sanitize_key( 'page' )] ) && TGM_Plugin_Activation::$instance->menu = $_GET[sanitize_key( 'page' )] ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    if ( ! class_exists( 'TGM_Bulk_Installer' ) ) {

    }

    if ( ! class_exists( 'TGM_Bulk_Installer_Skin' ) ) {

    }
}
