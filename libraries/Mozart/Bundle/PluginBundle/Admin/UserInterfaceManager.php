<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PluginBundle\Admin;

use Mozart\Bundle\PluginBundle\Model\PluginManager;
use Mozart\Component\Plugin\BulkInstaller;
use Mozart\Component\Plugin\Lister;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class PluginManagerUI
 * @package Mozart\Bundle\PluginBundle\Admin
 */
class UserInterfaceManager
{
    /**
     * @var array
     */
    private $messages = array();
    /**
     * @var PluginManager
     */
    private $pluginManager;

    /**
     * @var array
     */
    private $options;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param PluginManager $pluginManager
     * @param array         $options
     * @param RequestStack  $requestStack
     */
    public function __construct(
        PluginManager $pluginManager,
        array $options = array(),
        RequestStack $requestStack
    ) {
        $this->pluginManager = $pluginManager;
        $this->request = $requestStack->getCurrentRequest();
        $this->messages = $this->getDefaultMessages();
        $this->setOptions( $options );
    }

    /**
     * Amend action link after plugin installation.
     *
     * @param  array $install_actions Existing array of actions.
     * @return array Amended array of actions.
     */
    public function installPluginCompleteActions($install_actions)
    {
        // Remove action links on the TGMPA install page.
        if ($this->is_tgmpa_page()) {
            return false;
        }

        return $install_actions;

    }

    /**
     * Amend default configuration settings.
     *
     * @param array $options Array of config options to pass as class properties.
     */
    public function setOptions(array $options)
    {
        $keys = array(
            // Default absolute path to folder containing pre-packaged plugin zip files.
            'default_path',
            // Flag to show admin notices or not.
            'has_notices',
            // Flag to determine if the user can dismiss the notice nag.
            'dismissable',
            // Message to be output above nag notice if dismissable is false.
            'dismiss_msg',
            // Name of the querystring argument for the admin page.
            'menu',
            // Flag to set automatic activation of plugins. Off by default.
            'is_automatic',
            // Optional message to display before the plugins table.
            'message',
            'messages'
        );

        foreach ($keys as $key) {
            if (isset( $options[$key] )) {
                if (is_array( $options[$key] )) {
                    foreach ($options[$key] as $subkey => $value) {
                        $this->options[$key][$subkey] = $value;
                    }
                } else {
                    $this->options[$key] = $options[$key];
                }
            }
        }

    }

    public function getOption($name)
    {
        return $this->options[$name];
    }

    public function getMessage($name)
    {
        return $this->messages[$name];
    }

    /**
     * Determine if we're on the TGMPA Install page.
     *
     * @return boolean True when on the TGMPA page, false otherwise.
     */
    protected function is_tgmpa_page()
    {

        if ($this->options['menu'] === $this->request->get( 'page' )) {
            return true;
        }

        return false;

    }

    /**
     * Initialise the interactions between this class and WordPress.
     *
     * Hooks in three new methods for the class: admin_menu, notices and styles.
     *
     */
    public function init()
    {

        do_action( 'tgmpa_register' );
        // After this point, the plugins should be registered and the configuration set.

        // Proceed only if we have plugins to handle.
        if (count( $this->pluginManager->getPlugins() ) === 0) {
            return;
        }

        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_head', array( $this, 'dismiss' ) );
        add_filter( 'install_plugin_complete_actions', array( $this, 'installPluginCompleteActions' ) );
        /**
         * Flushes the plugins cache on theme switch to prevent stale entries
         * from remaining in the plugin table.
         */
        add_action(
            'switch_theme',
            function () {
                wp_cache_flush();
            }
        );

        // Load admin bar in the header to remove flash when installing plugins.
        if ($this->is_tgmpa_page()) {
            remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
            remove_action( 'admin_footer', 'wp_admin_bar_render', 1000 );
            add_action( 'wp_head', 'wp_admin_bar_render', 1000 );
            add_action( 'admin_head', 'wp_admin_bar_render', 1000 );
        }

        if ($this->options['has_notices']) {
            add_action( 'admin_notices', array( $this, 'notices' ) );
            add_action( 'admin_init', array( $this, 'admin_init' ), 1 );
            add_action( 'admin_enqueue_scripts', array( $this, 'thickbox' ) );
            add_action( 'switch_theme', array( $this, 'update_dismiss' ) );
        }

        // Setup the force activation hook.
        foreach ($this->pluginManager->getPlugins() as $plugin) {
            add_action( 'admin_init', array( $plugin, 'forceActivate' ) );
        }

        // Setup the force deactivation hook.
        foreach ($this->pluginManager->getPlugins() as $plugin) {
            add_action( 'switch_theme', array( $plugin, 'forceDeactivate' ) );
        }


    }


    /**
     * Handles calls to show plugin information via links in the notices.
     *
     * We get the links in the admin notices to point to the TGMPA page, rather
     * than the typical plugin-install.php file, so we can prepare everything
     * beforehand.
     *
     * WP doesn't make it easy to show the plugin information in the thickbox -
     * here we have to require a file that includes a function that does the
     * main work of displaying it, enqueue some styles, set up some globals and
     * finally call that function before exiting.
     *
     * @global string $tab Used as iframe div class names, helps with styling
     * @global string $body_id Used as the iframe body ID, helps with styling
     * @return null Returns early if not the TGMPA page.
     */
    public function admin_init()
    {

        if (!$this->is_tgmpa_page()) {
            return;
        }

        if ($this->request->get( 'tab' ) == 'plugin-information') {
            require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for install_plugin_information().

            wp_enqueue_style( 'plugin-install' );

            global $tab, $body_id;
            $body_id = $tab = 'plugin-information';

            install_plugin_information();

            exit;
        }

    }

    /**
     * Enqueues thickbox scripts/styles for plugin info.
     *
     * Thickbox is not automatically included on all admin pages, so we must
     * manually enqueue it for those pages.
     *
     * Thickbox is only loaded if the user has not dismissed the admin
     * notice or if there are any plugins left to install and activate.
     */
    public function thickbox()
    {

        if (!get_user_meta( get_current_user_id(), 'tgmpa_dismissed_notice', true )) {
            add_thickbox();
        }

    }

    /**
     * Adds submenu page under 'Appearance' tab.
     *
     * This method adds the submenu page letting users know that a required
     * plugin needs to be installed.
     *
     * This page disappears once the plugin has been installed and activated.
     */
    public function admin_menu()
    {
        // Make sure privileges are correct to see the page
        if (!current_user_can( 'install_plugins' )) {
            return;
        }

        if (count( $this->pluginManager->getInactivePlugins() ) > 0) {
            add_theme_page(
                $this->messages['page_title'], // Page title.
                $this->messages['menu_title'], // Menu title.
                'edit_theme_options', // Capability.
                $this->options['menu'], // Menu slug.
                array( $this, 'installPluginsPage' ) // Callback.
            );
        }

    }

    /**
     * Echoes required plugin notice.
     *
     * Outputs a message telling users that a specific plugin is required for
     * their theme. If appropriate, it includes a link to the form page where
     * users can install and activate the plugin.
     *
     * @global object $current_screen
     * @return null Returns early if we're on the Install page.
     */
    public function notices()
    {
        global $current_screen;

        // Remove nag on the install page.
        if ($this->is_tgmpa_page()) {
            return;
        }

        // Return early if the nag message has been dismissed.
        if (get_user_meta( get_current_user_id(), 'tgmpa_dismissed_notice', true )) {
            return;
        }

        $installed_plugins = get_plugins(); // Retrieve a list of all the plugins

        $message = array(); // Store the messages in an array to be outputted after plugins have looped through.
        $install_link = false; // Set to false, change to true in loop if conditions exist, used for action link 'install'.
        $install_link_count = 0; // Used to determine plurality of install action link text.
        $activate_link = false; // Set to false, change to true in loop if conditions exist, used for action link 'activate'.
        $activate_link_count = 0; // Used to determine plurality of activate action link text.

        foreach ($this->pluginManager->getPlugins() as $plugin) {
            // If the plugin is installed and active, check for minimum version argument before moving forward.
            if ($plugin->isActive()
                && $plugin->getVersion() !== ''
                && isset( $installed_plugins[$plugin->getBasename()]['Version'] )
                && version_compare(
                    $installed_plugins[$plugin->getBasename()]['Version'],
                    $plugin->getVersion(),
                    '<'
                )
            ) {
                if (current_user_can( 'install_plugins' )) {
                    $message['notice_ask_to_update'][] = $plugin->getSlug();
                } else {
                    $message['notice_cannot_update'][] = $plugin->getSlug();
                }
            }

            // Not installed.
            if (!isset( $installed_plugins[$plugin->getBasename()] )) {

                // We need to display the 'install' action link.
                $install_link = true;

                // Increment the install link count.
                $install_link_count++;

                if (current_user_can( 'install_plugins' )) {
                    if ($plugin->isRequired()) {
                        $message['notice_can_install_required'][] = $plugin->getSlug();
                    } else {
                        $message['notice_can_install_recommended'][] = $plugin->getSlug();
                    }
                } else {
                    $message['notice_cannot_install'][] = $plugin->getSlug();
                }
            }

            if (!$plugin->isActive()) {

                // We need to display the 'activate' action link.
                $activate_link = true;

                // Increment the activate link count.
                $activate_link_count++;

                if (current_user_can( 'activate_plugins' )) {
                    if ($plugin->isRequired()) {
                        $message['notice_can_activate_required'][] = $plugin->getSlug();
                    } else {
                        $message['notice_can_activate_recommended'][] = $plugin->getSlug();
                    }
                } else {
                    $message['notice_cannot_activate'][] = $plugin->getSlug();
                }
            }
        }

        // If we have notices to display, we move forward.
        if (!empty( $message )) {
            krsort( $message ); // Sort messages.
            $rendered = ''; // Display all nag messages as strings.

            // If dismissable is false and a message is set, output it now.
            if (!$this->options['dismissable'] && !empty( $this->options['dismiss_msg'] )) {
                $rendered .= '<p><strong>' . wp_kses_post( $this->options['dismiss_msg'] ) . '</strong></p>';
            }

            // Grab all plugin names.
            foreach ($message as $type => $plugin_groups) {
                $linked_plugin_groups = array();

                // Count number of plugins in each message group to calculate singular/plural message.
                $count = count( $plugin_groups );

                // Loop through the plugin names to make the ones pulled from the .org repo linked.
                foreach ($plugin_groups as $pluginSlug) {
                    $pluginGroupItem = $this->pluginManager->getPlugin( $pluginSlug );
                    $external_url = $pluginGroupItem->getExternalUrl();
                    $source = $pluginGroupItem->getSource();

                    if ($external_url && preg_match( '|^http(s)?://|', $external_url )) {
                        $linked_plugin_groups[] = '<a href="' . esc_url(
                                $external_url
                            ) . '" title="' . $pluginGroupItem->getName(
                            ) . '" target="_blank">' . $pluginGroupItem->getName() . '</a>';
                    } elseif (!$source || preg_match( '|^http://wordpress.org/extend/plugins/|', $source )) {
                        $url = add_query_arg(
                            array(
                                'tab'       => 'plugin-information',
                                'plugin'    => $pluginGroupItem,
                                'TB_iframe' => 'true',
                                'width'     => '640',
                                'height'    => '500',
                            ),
                            network_admin_url( 'plugin-install.php' )
                        );

                        $linked_plugin_groups[] = '<a href="' . esc_url(
                                $url
                            ) . '" class="thickbox" title="' . $pluginGroupItem->getName(
                            ) . '">' . $pluginGroupItem->getName() . '</a>';
                    } else {
                        $linked_plugin_groups[] = $pluginGroupItem->getName(); // No hyperlink.
                    }

                    if (isset( $linked_plugin_groups ) && (array) $linked_plugin_groups) {
                        $plugin_groups = $linked_plugin_groups;
                    }
                }

                $last_plugin = array_pop( $plugin_groups ); // Pop off last name to prep for readability.
                $imploded = empty( $plugin_groups ) ? '<em>' . $last_plugin . '</em>' : '<em>' . ( implode(
                            ', ',
                            $plugin_groups
                        ) . '</em> and <em>' . $last_plugin . '</em>' );

                $rendered .= '<p>' . sprintf(
                        translate_nooped_plural( $this->messages[$type], $count, 'tgmpa' ),
                        $imploded,
                        $count
                    ) . '</p>';
            }

// Setup variables to determine if action links are needed.
            $show_install_link = $install_link ? '<a href="' . add_query_arg(
                    'page',
                    $this->options['menu'],
                    network_admin_url( 'themes.php' )
                ) . '">' . translate_nooped_plural(
                    $this->messages['install_link'],
                    $install_link_count,
                    'tgmpa'
                ) . '</a>' : '';
            $show_activate_link = $activate_link ? '<a href="' . add_query_arg(
                    'page',
                    $this->options['menu'],
                    network_admin_url( 'themes.php' )
                ) . '">' . translate_nooped_plural(
                    $this->messages['activate_link'],
                    $activate_link_count,
                    'tgmpa'
                ) . '</a>' : '';

            // Define all of the action links.
            $action_links = apply_filters(
                'tgmpa_notice_action_links',
                array(
                    'install'  => ( current_user_can( 'install_plugins' ) ) ? $show_install_link : '',
                    'activate' => ( current_user_can( 'activate_plugins' ) ) ? $show_activate_link : '',
                    'dismiss'  => $this->options['dismissable'] ? '<a class="dismiss-notice" href="' . add_query_arg(
                            'action',
                            'dismiss_admin_notices'
                        ) . '" target="_parent">' . $this->messages['dismiss'] . '</a>' : '',
                )
            );

            $action_links = array_filter( $action_links ); // Remove any empty array items.
            if ($action_links) {
                $rendered .= '<p>' . implode( ' | ', $action_links ) . '</p>';
            }

            // Register the nag messages and prepare them to be processed.
            if (!empty( $this->messages['nag_type'] )) {
                add_settings_error(
                    'tgmpa',
                    'tgmpa',
                    $rendered,
                    sanitize_html_class( strtolower( $this->messages['nag_type'] ) )
                );
            } else {
                add_settings_error( 'tgmpa', 'tgmpa', $rendered, 'updated update-nag' );
            }
        }

        // Admin options pages already output settings_errors, so this is to avoid duplication.
        if ('options-general' !== $current_screen->parent_base) {
            settings_errors( 'tgmpa' );
        }

    }

    /**
     * Add dismissable admin notices.
     *
     * Appends a link to the admin nag messages. If clicked, the admin notice disappears and no longer is visible to users.
     */
    public function dismiss()
    {

        if ($this->request->get( 'action' ) == 'dismiss_admin_notices') {
            update_user_meta( get_current_user_id(), 'tgmpa_dismissed_notice', 1 );
        }

    }


    /**
     * Delete dismissable nag option when theme is switched.
     *
     * This ensures that the user is again reminded via nag of required
     * and/or recommended plugins if they re-activate the theme.
     */
    public function update_dismiss()
    {
        delete_user_meta( get_current_user_id(), 'tgmpa_dismissed_notice' );
    }


    /**
     * @return array
     */
    private function getDefaultMessages()
    {
        return array(
            'page_title'                      => __( 'Install Required Plugins', 'tgmpa' ),
            'menu_title'                      => __( 'Install Plugins', 'tgmpa' ),
            'installing'                      => __( 'Installing Plugin: %s', 'tgmpa' ),
            'oops'                            => __( 'Something went wrong.', 'tgmpa' ),
            'notice_can_install_required'     => _n_noop(
                'This theme requires the following plugin: %1$s.',
                'This theme requires the following plugins: %1$s.'
            ),
            'notice_can_install_recommended'  => _n_noop(
                'This theme recommends the following plugin: %1$s.',
                'This theme recommends the following plugins: %1$s.'
            ),
            'notice_cannot_install'           => _n_noop(
                'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.',
                'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.'
            ),
            'notice_can_activate_required'    => _n_noop(
                'The following required plugin is currently inactive: %1$s.',
                'The following required plugins are currently inactive: %1$s.'
            ),
            'notice_can_activate_recommended' => _n_noop(
                'The following recommended plugin is currently inactive: %1$s.',
                'The following recommended plugins are currently inactive: %1$s.'
            ),
            'notice_cannot_activate'          => _n_noop(
                'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.',
                'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.'
            ),
            'notice_ask_to_update'            => _n_noop(
                'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
                'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.'
            ),
            'notice_cannot_update'            => _n_noop(
                'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.',
                'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.'
            ),
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
            'return'                          => __( 'Return to Required Plugins Installer', 'tgmpa' ),
            'dashboard'                       => __( 'Return to the dashboard', 'tgmpa' ),
            'plugin_activated'                => __( 'Plugin activated successfully.', 'tgmpa' ),
            'activated_successfully'          => __( 'The following plugin was activated successfully:', 'tgmpa' ),
            'complete'                        => __(
                'All plugins installed and activated successfully. %1$s',
                'tgmpa'
            ),
            'dismiss'                         => __( 'Dismiss this notice', 'tgmpa' ),
        );


    }


    /**
     * Echoes plugin installation form.
     *
     * This method is the callback for the admin_menu method function.
     * This displays the admin page and form area where the user can select to install and activate the plugin.
     *
     * @return null Aborts early if we're processing a plugin installation action
     */
    public function installPluginsPage()
    {
        $bulkInstaller = new BulkInstaller();
        // Store new instance of plugin table in object.
        $plugin_table = new Lister( $this->pluginManager, $this, $bulkInstaller );

        // Return early if processing a plugin installation action.
        if (isset( $_POST['action'] )
            && 'tgmpa-bulk-install' == $_POST['action']
            && $plugin_table->process_bulk_actions() || $this->do_plugin_install()
        ) {
            return;
        }

        ?>
        <div class="tgmpa wrap">
            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
            <?php $plugin_table->prepare_items(); ?>

            <?php if (isset( $this->message )) {
                echo wp_kses_post( $this->message );
            } ?>

            <form id="tgmpa-plugins" action="" method="post">
                <input type="hidden" name="tgmpa-page" value="<?php echo $this->menu; ?>"/>
                <?php $plugin_table->display(); ?>
            </form>
        </div>
    <?php

    }

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
