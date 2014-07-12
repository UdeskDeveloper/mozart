<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Plugin;


class Installer {

    /**
     * Installs a plugin or activates a plugin depending on the hover
     * link clicked by the user.
     *
     * Checks the $_GET variable to see which actions have been
     * passed and responds with the appropriate method.
     *
     * Uses WP_Filesystem to process and handle the plugin installation
     * method.
     *
     * @return boolean True on success, false on failure
     */
    public function install()
    {

        // All plugin information will be stored in an array for processing.
        $plugin = array();

        // Checks for actions from hover links to process the installation.
        if (isset( $_GET['plugin'] ) && ( isset( $_GET['tgmpa-install'] ) && 'install-plugin' == $_GET['tgmpa-install'] )) {
            check_admin_referer( 'tgmpa-install' );

            $plugin['name'] = $_GET['plugin_name']; // Plugin name.
            $plugin['slug'] = $_GET['plugin']; // Plugin slug.
            $plugin['source'] = $_GET['plugin_source']; // Plugin source.

            // Pass all necessary information via URL if WP_Filesystem is needed.
            $url = wp_nonce_url(
                add_query_arg(
                    array(
                        'page'          => $this->menu,
                        'plugin'        => $plugin['slug'],
                        'plugin_name'   => $plugin['name'],
                        'plugin_source' => $plugin['source'],
                        'tgmpa-install' => 'install-plugin',
                    ),
                    network_admin_url( 'themes.php' )
                ),
                'tgmpa-install'
            );
            $method = ''; // Leave blank so WP_Filesystem can populate it as necessary.
            $fields = array( 'tgmpa-install' ); // Extra fields to pass to WP_Filesystem.

            if (false === ( $creds = request_filesystem_credentials( $url, $method, false, false, $fields ) )) {
                return true;
            }

            if (!WP_Filesystem( $creds )) {
                request_filesystem_credentials( $url, $method, true, false, $fields ); // Setup WP_Filesystem.
                return true;
            }

            require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for plugins_api.
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // Need for upgrade classes.

            // Set plugin source to WordPress API link if available.
            if (isset( $plugin['source'] ) && 'repo' == $plugin['source']) {
                $api = plugins_api(
                    'plugin_information',
                    array( 'slug' => $plugin['slug'], 'fields' => array( 'sections' => false ) )
                );

                if (is_wp_error( $api )) {
                    wp_die( $this->strings['oops'] . var_dump( $api ) );
                }

                if (isset( $api->download_link )) {
                    $plugin['source'] = $api->download_link;
                }
            }

            // Set type, based on whether the source starts with http:// or https://.
            $type = preg_match( '|^http(s)?://|', $plugin['source'] ) ? 'web' : 'upload';

            // Prep variables for Plugin_Installer_Skin class.
            $title = sprintf( $this->strings['installing'], $plugin['name'] );
            $url = add_query_arg( array( 'action' => 'install-plugin', 'plugin' => $plugin['slug'] ), 'update.php' );
            if (isset( $_GET['from'] )) {
                $url .= add_query_arg( 'from', urlencode( stripslashes( $_GET['from'] ) ), $url );
            }

            $nonce = 'install-plugin_' . $plugin['slug'];

            // Prefix a default path to pre-packaged plugins.
            $source = ( 'upload' == $type ) ? $this->default_path . $plugin['source'] : $plugin['source'];

            // Create a new instance of Plugin_Upgrader.
            $upgrader = new \Plugin_Upgrader(
                $skin = new \Plugin_Installer_Skin( compact( 'type', 'title', 'url', 'nonce', 'plugin', 'api' ) )
            );

            // Perform the action and install the plugin from the $source urldecode().
            $upgrader->install( $source );

            // Flush plugins cache so we can make sure that the installed plugins list is always up to date.
            wp_cache_flush();

            // Only activate plugins if the config option is set to true.
            if ($this->is_automatic) {
                $plugin_activate = $upgrader->plugin_info(); // Grab the plugin info from the Plugin_Upgrader method.
                $activate = activate_plugin( $plugin_activate ); // Activate the plugin.
                $this->populate_file_path(
                ); // Re-populate the file path now that the plugin has been installed and activated.

                if (is_wp_error( $activate )) {
                    echo '<div id="message" class="error"><p>' . $activate->get_error_message() . '</p></div>';
                    echo '<p><a href="' . add_query_arg(
                            'page',
                            $this->menu,
                            network_admin_url( 'themes.php' )
                        ) . '" title="' . esc_attr(
                            $this->strings['return']
                        ) . '" target="_parent">' . $this->strings['return'] . '</a></p>';

                    return true; // End it here if there is an error with automatic activation
                } else {
                    echo '<p>' . $this->strings['plugin_activated'] . '</p>';
                }
            }

            // Display message based on if all plugins are now active or not.
            $complete = array();
            foreach ($this->plugins as $plugin) {
                if (!is_plugin_active( $plugin['file_path'] )) {
                    echo '<p><a href="' . add_query_arg(
                            'page',
                            $this->menu,
                            network_admin_url( 'themes.php' )
                        ) . '" title="' . esc_attr(
                            $this->strings['return']
                        ) . '" target="_parent">' . $this->strings['return'] . '</a></p>';
                    $complete[] = $plugin;
                    break;
                } // Nothing to store.
                else {
                    $complete[] = '';
                }
            }

            // Filter out any empty entries.
            $complete = array_filter( $complete );

            // All plugins are active, so we display the complete string and hide the plugin menu.
            if (empty( $complete )) {
                echo '<p>' . sprintf(
                        $this->strings['complete'],
                        '<a href="' . network_admin_url() . '" title="' . __(
                            'Return to the Dashboard',
                            'tgmpa'
                        ) . '">' . __( 'Return to the Dashboard', 'tgmpa' ) . '</a>'
                    ) . '</p>';
                echo '<style type="text/css">#adminmenu .wp-submenu li.current { display: none !important; }</style>';
            }

            return true;
        } // Checks for actions from hover links to process the activation.
        elseif (isset( $_GET['plugin'] ) && ( isset( $_GET['tgmpa-activate'] ) && 'activate-plugin' == $_GET['tgmpa-activate'] )) {
            check_admin_referer( 'tgmpa-activate', 'tgmpa-activate-nonce' );

            // Populate $plugin array with necessary information.
            $plugin['name'] = $_GET['plugin_name'];
            $plugin['slug'] = $_GET['plugin'];
            $plugin['source'] = $_GET['plugin_source'];

            $plugin_data = get_plugins( '/' . $plugin['slug'] ); // Retrieve all plugins.
            $plugin_file = array_keys( $plugin_data ); // Retrieve all plugin files from installed plugins.
            $plugin_to_activate = $plugin['slug'] . '/' . $plugin_file[0]; // Match plugin slug with appropriate plugin file.
            $activate = activate_plugin( $plugin_to_activate ); // Activate the plugin.

            if (is_wp_error( $activate )) {
                echo '<div id="message" class="error"><p>' . $activate->get_error_message() . '</p></div>';
                echo '<p><a href="' . add_query_arg(
                        'page',
                        $this->menu,
                        network_admin_url( 'themes.php' )
                    ) . '" title="' . esc_attr(
                        $this->strings['return']
                    ) . '" target="_parent">' . $this->strings['return'] . '</a></p>';

                return true; // End it here if there is an error with activation.
            } else {
                // Make sure message doesn't display again if bulk activation is performed immediately after a single activation.
                if (!isset( $_POST['action'] )) {
                    $msg = $this->strings['activated_successfully'] . ' <strong>' . $plugin['name'] . '</strong>';
                    echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
                }
            }
        }

        return false;

    }
} 