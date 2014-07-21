<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\OptionBundle\Controller;

use Mozart\Component\Option\OptionBuilderInterface;

/**
 * Class OptionController
 * @package Mozart\Bundle\OptionBundle\Controller
 */
class OptionController
{
    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var OptionBuilderInterface
     */
    private $optionBuilder;

    private $notices;

    public function __construct(
        OptionBuilderInterface $optionBuilder,
        $parameters
    ) {
        $this->notices = array();
        $this->optionBuilder = $optionBuilder;
        $this->parameters = $parameters;
    }

    public function initOptionManager()
    {
        if (!isset( $this->parameters['opt_name'] )) {
            return;
        }

        $this->optionBuilder->boot( $this->parameters );
        $this->setOptions();
        $this->startHooks();
    }

    public function getBuilder()
    {
        return $this->optionBuilder;
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
                    $this->options = get_site_option( 'ReduxFrameworkPlugin', $defaults );
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
     * @return void
     */
    public function startHooks()
    {
        // Options page
        add_action( 'admin_menu', array( $this->optionBuilder, '_options_page' ) );

        // Add a network menu
        if ($this->optionBuilder->getParam( 'database' ) == "network"
            && $this->optionBuilder->getParam( 'network_admin' )
        ) {
            add_action( 'network_admin_menu', array( $this->optionBuilder, '_options_page' ) );
        }

        // Admin Bar menu
        add_action( 'admin_bar_menu', array( $this->optionBuilder, '_admin_bar_menu' ), 999 );

        // Register setting
        add_action( 'admin_init', array( $this->optionBuilder, '_register_settings' ) );


        // Display admin notices
        add_action( 'admin_notices', array( $this, 'adminNotices' ) );

        // Check for dismissed admin notices.
        add_action( 'admin_init', array( $this, 'dismissAdminNotice' ), 9 );

        // Enqueue the admin page CSS and JS
        if (isset( $_GET['page'] ) && $_GET['page'] == $this->optionBuilder->getParam( 'page_slug' )) {
            add_action( 'admin_enqueue_scripts', array( $this->optionBuilder, '_enqueue' ), 1 );
        }

        // Output dynamic CSS
        add_action( 'wp_head', array( $this->optionBuilder, '_output_css' ), 150 );

        // Enqueue dynamic CSS and Google fonts
        add_action( 'wp_enqueue_scripts', array( $this->optionBuilder, '_enqueue_output' ), 150 );


        if ($this->optionBuilder->getParam( 'database' ) == "network"
            && $this->optionBuilder->getParam( 'network_admin' )
        ) {
            add_action(
                'network_admin_edit_redux_' . $this->optionBuilder->getParam( 'opt_name' ),
                array(
                    $this->optionBuilder,
                    'save_network_page'
                ),
                10,
                0
            );
            add_action( 'admin_bar_menu', array( $this->optionBuilder, 'network_admin_bar' ), 999 );

        }
        add_action( 'wp_loaded', array( $this, 'options_toggle_check' ) );

        // Activate plugin when new blog is added
        add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

        add_action( 'activated_plugin', array( $this, 'load_first' ) );

        do_action( 'redux/plugin/hooks', $this );
    }


    /**
     * Evaluates user dismiss option for displaying admin notices
     *
     * @return void
     */
    public function adminNotices()
    {
        global $current_user, $pagenow;

        // Enum admin notices
        foreach ($this->notices as $notice) {
            if (true == $notice['dismiss']) {

                // Get user ID
                $userid = $current_user->ID;

                if (!get_user_meta( $userid, 'ignore_' . $notice['id'] )) {

                    // Check if we are on admin.php.  If we are, we have
                    // to get the current page slug and tab, so we can
                    // feed it back to Wordpress.  Why>  admin.php cannot
                    // be accessed without the page parameter.  We add the
                    // tab to return the user to the last panel they were
                    // on.
                    $pageName = '';
                    $curTab = '';
                    if ($pagenow == 'admin.php' || $pagenow == 'themes.php') {

                        // Get the current page.  To avoid errors, we'll set
                        // the redux page slug if the GET is empty.
                        $pageName = empty( $_GET['page'] ) ? '&amp;page=' . $this->optionBuilder->getParam('page_slug') : '&amp;page=' . $_GET['page'];

                        // Ditto for the current tab.
                        $curTab = empty( $_GET['tab'] ) ? '&amp;tab=0' : '&amp;tab=' . $_GET['tab'];
                    }

                    // Print the notice with the dismiss link
                    echo '<div class="' . $notice['type'] . '"><p>' . $notice['msg'] . '&nbsp;&nbsp;<a href="?dismiss=true&amp;id=' . $notice['id'] . $pageName . $curTab . '">' . __(
                            'Dismiss',
                            'mozart-options'
                        ) . '</a>.</p></div>';
                }
            } else {

                // Standard notice
                echo '<div class="' . $notice['type'] . '"><p>' . $notice['msg'] . '</a>.</p></div>';
            }
        }

        // Clear the admin notice array
        $this->notices = array();
    }

    /**
     * Updates user meta to store dismiss notice preference
     *
     * @return void
     */
    public function dismissAdminNotice()
    {
        global $current_user;

        // Verify the dismiss and id parameters are present.
        if (isset( $_GET['dismiss'] ) && isset( $_GET['id'] )) {
            if ('true' == $_GET['dismiss'] || 'false' == $_GET['dismiss']) {

                // Get the user id
                $userid = $current_user->ID;

                // Get the notice id
                $id = $_GET['id'];
                $val = $_GET['dismiss'];

                // Add the dismiss request to the user meta.
                update_user_meta( $userid, 'ignore_' . $id, $val );
            }
        }
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

    /**
     *
     */
    public function single_activate()
    {

    }

    /**
     *
     */
    public function single_deactivate()
    {

    }

    /**
     * Get all IDs of blogs that are not activated, not spam, and not deleted
     *
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
