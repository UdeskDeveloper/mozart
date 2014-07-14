<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\OptionBundle\Controller;

use Mozart\Component\Option\OptionBuilderInterface;
use Mozart\Bundle\OptionBundle\Extension\ExtensionManager;
use Mozart\Bundle\OptionBundle\SectionManager;

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
    protected $sections = array();

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var SectionManager
     */
    private $sectionManager;

    /**
     * @var ExtensionManager
     */
    private $extensionManager;

    /**
     * @var OptionBuilderInterface
     */
    private $optionBuilder;

    /**
     * @param SectionManager $sectionManager
     * @param ExtensionManager $extensionManager
     */
    public function __construct(
        OptionBuilderInterface $optionBuilder,
        SectionManager $sectionManager,
        ExtensionManager $extensionManager,
        $parameters
    ) {
        $this->optionBuilder = $optionBuilder;
        $this->sectionManager = $sectionManager;
        $this->extensionManager = $extensionManager;
        $this->parameters = $parameters;
    }

    public function init()
    {
        add_action( "redux/extensions/mozart-options/before", array( $this, 'loadExtensions' ) );

        $this->sections = $this->sectionManager->getSections();

        if (!isset( $this->parameters['opt_name'] )) {
            return;
        }

        $this->optionBuilder->boot( $this->sections, $this->parameters );
        $this->setOptions();
        $this->startHooks();
    }

    public function loadExtensions()
    {
        foreach ($this->extensionManager->getExtensions() as $extension) {
            $extension->boot();
        }
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
        add_action( 'wp_loaded', array( $this, 'options_toggle_check' ) );

        // Activate plugin when new blog is added
        add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

        // Display admin notices
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );

        add_action( 'activated_plugin', array( $this, 'load_first' ) );

        do_action( 'redux/plugin/hooks', $this );
    }

    /**
     *
     */
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
     * @param boolean $network_wide True if plugin is network activated, false otherwise
     *
     * @return void
     */
    public function activate( $network_wide )
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
    public function deactivate( $network_wide )
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
    public function activate_new_site( $blog_id )
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
    public function add_action_links( $links )
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
