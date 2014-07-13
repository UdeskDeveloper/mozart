<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PluginBundle\Model;

use Mozart\Component\Plugin\PluginInterface;

/**
 * Class Plugin
 * @package Mozart\Bundle\PluginBundle\Model
 */
class Plugin implements PluginInterface
{
    /**
     * @var string
     */
    private $name = '';
    /**
     * @var string
     */
    private $slug = '';
    /**
     * @var string
     */
    private $source = '';
    /**
     * @var bool
     */
    private $required = false;
    /**
     * @var string
     */
    private $version = '';
    /**
     * @var bool
     */
    private $force_activation = false;
    /**
     * @var bool
     */
    private $force_deactivation = false;
    /**
     * @var string
     */
    private $external_url = '';

    private $basename = '';

    /**
     * @return string
     */
    public function getExternalUrl()
    {
        return $this->external_url;
    }

    /**
     * @param string $external_url
     */
    public function setExternalUrl($external_url)
    {
        $this->external_url = $external_url;
    }

    /**
     * @return boolean
     */
    public function isForceActivation()
    {
        return $this->force_activation;
    }

    /**
     * @param boolean $force_activation
     */
    public function setForceActivation($force_activation)
    {
        $this->force_activation = $force_activation;
    }

    /**
     * @return boolean
     */
    public function isForceDeactivation()
    {
        return $this->force_deactivation;
    }

    /**
     * @param boolean $force_deactivation
     */
    public function setForceDeactivation($force_deactivation)
    {
        $this->force_deactivation = $force_deactivation;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Helper function to extract the file path of the plugin file from the
     * plugin slug, if the plugin is installed.
     *
     * @return string Either file path for plugin if installed, or just the plugin slug.
     */
    public function getBasename()
    {
        if ('' === $this->basename) {
            $keys = array_keys( get_plugins() );

            foreach ($keys as $key) {
                if (preg_match( '|^' . $this->slug . '/|', $key )) {
                    $this->basename = $key;
                }
            }

            $this->basename = $this->slug;
        }

        return $this->basename;

    }

    /**
     * Forces plugin activation if the parameter 'force_activation' is
     * set to true.
     *
     * This allows theme authors to specify certain plugins that must be
     * active at all times while using the current theme.
     *
     * Please take special care when using this parameter as it has the
     * potential to be harmful if not used correctly. Setting this parameter
     * to true will not allow the specified plugin to be deactivated unless
     * the user switches themes.
     */
    public function forceActivate()
    {
        if (false == $this->force_activation) {
            return;
        }

        $installed_plugins = get_plugins();
        $pluginPath = $this->getBasename();

        if (!isset( $installed_plugins[$pluginPath] )
            || is_plugin_active( $pluginPath )
        ) {
            return;
        }

        activate_plugin( $pluginPath );
    }

    /**
     * Forces plugin deactivation if the parameter 'force_deactivation'
     * is set to true.
     *
     * This allows theme authors to specify certain plugins that must be
     * deactived upon switching from the current theme to another.
     *
     * Please take special care when using this parameter as it has the
     * potential to be harmful if not used correctly.
     */
    public function forceDeactivate()
    {
        if (false == $this->force_deactivation) {
            return;
        }

        $pluginPath = $this->getBasename();

        if (is_plugin_active( $pluginPath )) {
            deactivate_plugins( $pluginPath );
        }
    }

    public function isActive()
    {
        return is_plugin_active( $this->getBasename() );
    }

    public function install($options)
    {

        check_admin_referer( 'tgmpa-install' );

        $plugin['name'] = $_GET['plugin_name']; // Plugin name.
        $plugin['slug'] = $_GET['plugin']; // Plugin slug.
        $plugin['source'] = $_GET['plugin_source']; // Plugin source.

        // Pass all necessary information via URL if WP_Filesystem is needed.
        $url = wp_nonce_url(
            add_query_arg(
                array(
                    'page'          => $options['menu'],
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
                wp_die( $this->messages['oops'] . var_dump( $api ) );
            }

            if (isset( $api->download_link )) {
                $plugin['source'] = $api->download_link;
            }
        }

        // Set type, based on whether the source starts with http:// or https://.
        $type = preg_match( '|^http(s)?://|', $plugin['source'] ) ? 'web' : 'upload';

        // Prep variables for Plugin_Installer_Skin class.
        $title = sprintf( $this->messages['installing'], $plugin['name'] );
        $url = add_query_arg( array( 'action' => 'install-plugin', 'plugin' => $plugin['slug'] ), 'update.php' );
        if (isset( $_GET['from'] )) {
            $url .= add_query_arg( 'from', urlencode( stripslashes( $_GET['from'] ) ), $url );
        }

        $nonce = 'install-plugin_' . $plugin['slug'];

        // Prefix a default path to pre-packaged plugins.
        $source = ( 'upload' == $type ) ? $options['default_path'] . $plugin['source'] : $plugin['source'];

        // Create a new instance of Plugin_Upgrader.
        $upgrader = new \Plugin_Upgrader(
            $skin = new \Plugin_Installer_Skin( compact( 'type', 'title', 'url', 'nonce', 'plugin', 'api' ) )
        );

        // Perform the action and install the plugin from the $source urldecode().
        $upgrader->install( $source );

        // Flush plugins cache so we can make sure that the installed plugins list is always up to date.
        wp_cache_flush();

        // Only activate plugins if the config option is set to true.
        if ($options['is_automatic']) {
            $plugin_activate = $upgrader->plugin_info(); // Grab the plugin info from the Plugin_Upgrader method.
            $activate = activate_plugin( $plugin_activate ); // Activate the plugin.
            $this->populate_file_path(
            ); // Re-populate the file path now that the plugin has been installed and activated.

            if (is_wp_error( $activate )) {
                echo '<div id="message" class="error"><p>' . $activate->get_error_message() . '</p></div>';
                echo '<p><a href="' . add_query_arg(
                        'page',
                        $options['menu'],
                        network_admin_url( 'themes.php' )
                    ) . '" title="' . esc_attr(
                        $this->messages['return']
                    ) . '" target="_parent">' . $this->messages['return'] . '</a></p>';

                return true; // End it here if there is an error with automatic activation
            } else {
                echo '<p>' . $this->messages['plugin_activated'] . '</p>';
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
                        $this->messages['return']
                    ) . '" target="_parent">' . $this->messages['return'] . '</a></p>';
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
                    $this->messages['complete'],
                    '<a href="' . network_admin_url() . '" title="' . __(
                        'Return to the Dashboard',
                        'tgmpa'
                    ) . '">' . __( 'Return to the Dashboard', 'tgmpa' ) . '</a>'
                ) . '</p>';
            echo '<style type="text/css">#adminmenu .wp-submenu li.current { display: none !important; }</style>';
        }

        return true;
    }
}
