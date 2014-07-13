<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\OptionBundle\Redux;

/**
 * Class Redux
 *
 * @package Mozart\Bundle\OptionBundle\Redux
 */
class Redux
{

    /**
     * @access      protected
     * @var         array $options Array of config options, used to check for demo mode
     */
    protected $options = array();

    /**
     * Use this value as the text domain when translating strings from this plugin. It should match
     * the Text Domain field set in the plugin header, as well as the directory name of the plugin.
     * Additionally, text domains should only contain letters, number and hypens, not underscores
     * or spaces.
     *
     * @access      protected
     * @var         string $plugin_slug The unique ID (slug) of this plugin
     */
    protected $plugin_slug = 'redux-framework';

    /**
     * @access      protected
     * @var         string $plugin_screen_hook_suffix The slug of the plugin screen
     */
    protected $plugin_screen_hook_suffix = null;

    /**
     * @access      protected
     * @var         string $plugin_network_activated Check for plugin network activation
     */
    protected $plugin_network_activated = null;

    /**
     *
     */
    public function init()
    {
        $this->setOptions();
        $this->startHooks();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        if (empty( $this->options )) {
            $this->setOptions();
        }

        return $this->options;
    }

    /**
     * Get Redux options
     *
     * @access      public
     * @since       3.1.3
     * @return void
     */
    public function setOptions()
    {

        // Setup defaults
        $defaults = array();

        // If multisite is enabled
        if (is_multisite()) {

            // Get network activated plugins
            $plugins = get_site_option( 'active_sitewide_plugins' );

            foreach ($plugins as $file => $plugin) {
                if (strpos( $file, 'redux-framework.php' ) !== false) {
                    $this->plugin_network_activated = true;
                    $this->options                  = get_site_option( 'ReduxFrameworkPlugin', $defaults );
                }
            }
        }

        // If options aren't set, grab them now!
        if (empty( $this->options )) {
            $this->options = get_option( 'ReduxFrameworkPlugin', $defaults );
        }
    }

    /**
     * Run action and filter hooks
     *
     * @access      private
     * @since       3.1.3
     * @return void
     */
    public function startHooks()
    {
        add_action( 'wp_loaded', array( $this, 'options_toggle_check' ) );

        // Activate plugin when new blog is added
        add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

        // Display admin notices
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );

        add_action( 'activated_plugin', array( $this, 'load_first' ) );

        do_action( 'redux/plugin/hooks', $this );
    }

    public function admin_notices()
    {
    }

    /**
     *
     */
    public function load_first()
    {
        $path = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
        if ($plugins = get_option( 'active_plugins' )) {
            if ($key = array_search( $path, $plugins )) {
                array_splice( $plugins, $key, 1 );
                array_unshift( $plugins, $path );
                update_option( 'active_plugins', $plugins );
            }
        }
    }

    /**
     * Fired on plugin activation
     *
     * @access      public
     * @since       3.0.0
     *
     * @param boolean $network_wide True if plugin is network activated, false otherwise
     *
     * @return void
     */
    public function activate($network_wide)
    {
        if (function_exists( 'is_multisite' ) && is_multisite()) {
            if ($network_wide) {
                // Get all blog IDs
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {
                    switch_to_blog( $blog_id );
                    $this->single_activate();
                }
                restore_current_blog();
            } else {
                $this->single_activate();
            }
        } else {
            $this->single_activate();
        }

        delete_site_transient( 'update_plugins' );
    }

    /**
     * Fired when plugin is deactivated
     *
     * @access      public
     * @since       3.0.0
     *
     * @param boolean $network_wide True if plugin is network activated, false otherwise
     *
     * @return void
     */
    public function deactivate($network_wide)
    {
        if (function_exists( 'is_multisite' ) && is_multisite()) {
            if ($network_wide) {
                // Get all blog IDs
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {
                    switch_to_blog( $blog_id );
                    $this->single_deactivate();
                }
                restore_current_blog();
            } else {
                $this->single_deactivate();
            }
        } else {
            $this->single_deactivate();
        }

        delete_option( 'ReduxFrameworkPlugin' );
    }

    /**
     * Fired when a new WPMU site is activated
     *
     * @access      public
     * @since       3.0.0
     *
     * @param int $blog_id The ID of the new blog
     *
     * @return void
     */
    public function activate_new_site($blog_id)
    {
        if (1 !== did_action( 'wpmu_new_blog' )) {
            return;
        }

        switch_to_blog( $blog_id );
        $this->single_activate();
        restore_current_blog();
    }

    public function single_activate()
    {

    }

    public function single_deactivate()
    {

    }

    /**
     * Get all IDs of blogs that are not activated, not spam, and not deleted
     *
     * @access      private
     * @since       3.0.0
     * @global      object $wpdb
     * @return array|false Array of IDs or false if none are found
     */
    private static function get_blog_ids()
    {
        global $wpdb;

        // Get an array of IDs
        $sql = "SELECT blog_id FROM $wpdb->blogs
                    WHERE archived = '0' AND spam = '0'
                    AND deleted = '0'";

        return $wpdb->get_col( $sql );
    }

    /**
     * Turn on or off
     *
     * @access      public
     * @since       3.0.0
     * @global      string $pagenow The current page being displayed
     * @return void
     */
    public function options_toggle_check()
    {
        global $pagenow;

        if ($pagenow == 'plugins.php' && is_admin() && !empty( $_GET['ReduxFrameworkPlugin'] )) {
            $url = './plugins.php';

            if ($_GET['ReduxFrameworkPlugin'] == 'demo') {
                if ($this->options['demo'] == false) {
                    $this->options['demo'] = true;
                } else {
                    $this->options['demo'] = false;
                }
            }

            if (is_multisite() && is_network_admin() && $this->plugin_network_activated) {
                update_site_option( 'ReduxFrameworkPlugin', $this->options );
            } else {
                update_option( 'ReduxFrameworkPlugin', $this->options );
            }

            wp_redirect( $url );
        }
    }

    /**
     * Add settings action link to plugins page
     */
    public function add_action_links($links)
    {
        // In case we ever want to do this...
        return $links;

        /**
         * return array_merge(
         *      array( 'redux_plugin_settings' => '<a href="' . admin_url( 'plugins.php?page=' . 'redux_plugin_settings' ) . '">' . __( 'Settings', 'redux-framework' ) . '</a>' ),
         *      $links
         * );
         */
    }
}
