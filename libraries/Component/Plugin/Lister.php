<?php

namespace Mozart\Component\Plugin;

use Mozart\Bundle\PluginBundle\Admin\UserInterfaceManager;
use Mozart\Bundle\PluginBundle\Model\PluginManager;

if (false === class_exists( '\WP_List_Table' )) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * List table class for handling plugins.
 *
 * Extends the WP_List_Table class to provide a future-compatible
 * way of listing out all required/recommended plugins.
 *
 * Gives users an interface similar to the Plugin Administration
 * area with similar (albeit stripped down) capabilities.
 *
 * This class also allows for the bulk install of plugins.
 *
 * @author  Thomas Griffin <thomas@thomasgriffinmedia.com>
 * @author  Gary Jones <gamajo@gamajo.com>
 */
class Lister extends \WP_List_Table
{

    /**
     * @var UserInterfaceManager
     */
    private $userInterfaceManager;
    /**
     * @var BulkInstaller
     */
    private $bulkInstaller;
    /**
     * @var PluginManager
     */
    private $pluginManager;

    /**
     * References parent constructor and sets defaults for class.
     *
     */
    public function __construct(
        PluginManager $pluginManager,
        UserInterfaceManager $userInterfaceManager,
        BulkInstaller $bulkInstaller
    )
    {
        $this->pluginManager = $pluginManager;
        $this->userInterfaceManager = $userInterfaceManager;
        $this->bulkInstaller = $bulkInstaller;

        parent::__construct(
            array(
                'singular' => 'plugin',
                'plural'   => 'plugins',
                'ajax'     => false,
            )
        );

    }

    /**
     * Gathers and renames all of our plugin information to be used by
     * WP_List_Table to create our table.
     *
     * @since 2.2.0
     *
     * @return array $table_data Information for use in table.
     */
    protected function _gather_plugin_data()
    {

        // Load thickbox for plugin links.
        $this->userInterfaceManager->admin_init();
        $this->userInterfaceManager->thickbox();

        // Prep variables for use and grab list of all installed plugins.
        $table_data = array();
        $i = 0;
        $installed_plugins = get_plugins();

        foreach ($this->$pluginManager->getPlugins() as $plugin) {
            if (is_plugin_active( $plugin['file_path'] )) {
                continue; // No need to display plugins if they are installed and activated.
            }

            $table_data[$i]['sanitized_plugin'] = $plugin['name'];
            $table_data[$i]['slug'] = $this->_get_plugin_data_from_name( $plugin['name'] );

            $external_url = $this->_get_plugin_data_from_name( $plugin['name'], 'external_url' );
            $source = $this->_get_plugin_data_from_name( $plugin['name'], 'source' );

            if ($external_url && preg_match( '|^http(s)?://|', $external_url )) {
                $table_data[$i]['plugin'] = '<strong><a href="' . esc_url(
                        $external_url
                    ) . '" title="' . $plugin['name'] . '" target="_blank">' . $plugin['name'] . '</a></strong>';
            } elseif (!$source || preg_match( '|^http://wordpress.org/extend/plugins/|', $source )) {
                $url = add_query_arg(
                    array(
                        'tab'       => 'plugin-information',
                        'plugin'    => $this->_get_plugin_data_from_name( $plugin['name'] ),
                        'TB_iframe' => 'true',
                        'width'     => '640',
                        'height'    => '500',
                    ),
                    network_admin_url( 'plugin-install.php' )
                );

                $table_data[$i]['plugin'] = '<strong><a href="' . esc_url(
                        $url
                    ) . '" class="thickbox" title="' . $plugin['name'] . '">' . $plugin['name'] . '</a></strong>';
            } else {
                $table_data[$i]['plugin'] = '<strong>' . $plugin['name'] . '</strong>'; // No hyperlink.
            }

            if (isset( $table_data[$i]['plugin'] ) && (array) $table_data[$i]['plugin']) {
                $plugin['name'] = $table_data[$i]['plugin'];
            }

            if (!empty( $plugin['source'] )) {
                // The plugin must be from a private repository.
                if (preg_match( '|^http(s)?://|', $plugin['source'] )) {
                    $table_data[$i]['source'] = __( 'Private Repository', 'tgmpa' );
                    // The plugin is pre-packaged with the theme.
                } else {
                    $table_data[$i]['source'] = __( 'Pre-Packaged', 'tgmpa' );
                }
            } // The plugin is from the WordPress repository.
            else {
                $table_data[$i]['source'] = __( 'WordPress Repository', 'tgmpa' );
            }

            $table_data[$i]['type'] = isset( $plugin['required'] ) && $plugin['required'] ? __(
                'Required',
                'tgmpa'
            ) : __( 'Recommended', 'tgmpa' );

            if (!isset( $installed_plugins[$plugin['file_path']] )) {
                $table_data[$i]['status'] = sprintf( '%1$s', __( 'Not Installed', 'tgmpa' ) );
            } elseif (is_plugin_inactive( $plugin['file_path'] )) {
                $table_data[$i]['status'] = sprintf( '%1$s', __( 'Installed But Not Activated', 'tgmpa' ) );
            }

            $table_data[$i]['file_path'] = $plugin['file_path'];
            $table_data[$i]['url'] = isset( $plugin['source'] ) ? $plugin['source'] : 'repo';

            $i++;
        }

        // Sort plugins by Required/Recommended type and by alphabetical listing within each type.
        $resort = array();
        $req = array();
        $rec = array();

        // Grab all the plugin types.
        foreach ($table_data as $plugin) {
            $resort[] = $plugin['type'];
        }

        // Sort each plugin by type.
        foreach ($resort as $type) {
            if ('Required' == $type) {
                $req[] = $type;
            } else {
                $rec[] = $type;
            }
        }

        // Sort alphabetically each plugin type array, merge them and then sort in reverse (lists Required plugins first).
        sort( $req );
        sort( $rec );
        array_merge( $resort, $req, $rec );
        array_multisort( $resort, SORT_DESC, $table_data );

        return $table_data;

    }

    /**
     * Retrieve plugin data, given the plugin name. Taken from the
     * Activation class.
     *
     * Loops through the registered plugins looking for $name. If it finds it,
     * it returns the $data from that plugin. Otherwise, returns false.
     *
     * @since 2.2.0
     *
     * @param  string         $name Name of the plugin, as it was registered.
     * @param  string         $data Optional. Array key of plugin data to return. Default is slug.
     * @return string|boolean Plugin slug if found, false otherwise.
     */
    protected function _get_plugin_data_from_name($name, $data = 'slug')
    {

        foreach ($this->pluginManager->getPlugins() as $plugin => $values) {
            if ($name == $values['name'] && isset( $values[$data] )) {
                return $values[$data];
            }
        }

        return false;

    }

    /**
     * Create default title column along with action links of 'Install'
     * and 'Activate'.
     *
     * @param  array  $item Array of item data.
     * @return string The action hover links.
     */
    public function column_plugin($item)
    {
        $installed_plugins = get_plugins();
        $actions = array();

        // We need to display the 'Install' hover link.
        if (!isset( $installed_plugins[$item['file_path']] )) {
            $actions = array(
                'install' => sprintf(
                    '<a href="%1$s" title="' . __( 'Install', 'tgmpa' ) . ' %2$s">' . __( 'Install', 'tgmpa' ) . '</a>',
                    wp_nonce_url(
                        add_query_arg(
                            array(
                                'page'          => $this->userInterfaceManager->getOption('menu'),
                                'plugin'        => $item['slug'],
                                'plugin_name'   => $item['sanitized_plugin'],
                                'plugin_source' => $item['url'],
                                'tgmpa-install' => 'install-plugin',
                            ),
                            network_admin_url( 'themes.php' )
                        ),
                        'tgmpa-install'
                    ),
                    $item['sanitized_plugin']
                ),
            );
        } // We need to display the 'Activate' hover link.
        elseif (is_plugin_inactive( $item['file_path'] )) {
            $actions = array(
                'activate' => sprintf(
                    '<a href="%1$s" title="' . __( 'Activate', 'tgmpa' ) . ' %2$s">' . __(
                        'Activate',
                        'tgmpa'
                    ) . '</a>',
                    add_query_arg(
                        array(
                            'page'                 => $this->userInterfaceManager->getOption('menu'),
                            'plugin'               => $item['slug'],
                            'plugin_name'          => $item['sanitized_plugin'],
                            'plugin_source'        => $item['url'],
                            'tgmpa-activate'       => 'activate-plugin',
                            'tgmpa-activate-nonce' => wp_create_nonce( 'tgmpa-activate' ),
                        ),
                        network_admin_url( 'themes.php' )
                    ),
                    $item['sanitized_plugin']
                ),
            );
        }

        return sprintf( '%1$s %2$s', $item['plugin'], $this->row_actions( $actions ) );

    }

    /**
     * Required for bulk installing.
     *
     * Adds a checkbox for each plugin.
     *
     * @param  array  $item Array of item data.
     * @return string The input checkbox with all necessary info.
     */
    public function column_cb($item)
    {

        $value = $item['file_path'] . ',' . $item['url'] . ',' . $item['sanitized_plugin'];

        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" id="%3$s" />',
            $this->_args['singular'],
            $value,
            $item['sanitized_plugin']
        );

    }

    /**
     * Sets default message within the plugins table if no plugins
     * are left for interaction.
     *
     * Hides the menu item to prevent the user from clicking and
     * getting a permissions error.
     */
    public function no_items()
    {

        printf(
            __(
                'No plugins to install or activate. <a href="%1$s" title="Return to the Dashboard">Return to the Dashboard</a>',
                'tgmpa'
            ),
            network_admin_url()
        );
        echo '<style type="text/css">#adminmenu .wp-submenu li.current { display: none !important; }</style>';

    }

    /**
     * Output all the column information within the table.
     *
     * @return array $columns The column names.
     */
    public function get_columns()
    {

        $columns = array(
            'cb'     => '<input type="checkbox" />',
            'plugin' => __( 'Plugin', 'tgmpa' ),
            'source' => __( 'Source', 'tgmpa' ),
            'type'   => __( 'Type', 'tgmpa' ),
            'status' => __( 'Status', 'tgmpa' )
        );

        return $columns;

    }

    /**
     * Defines all types of bulk actions for handling
     * registered plugins.
     *
     * @return array $actions The bulk actions for the plugin install table.
     */
    public function get_bulk_actions()
    {

        $actions = array(
            'tgmpa-bulk-install'  => __( 'Install', 'tgmpa' ),
            'tgmpa-bulk-activate' => __( 'Activate', 'tgmpa' ),
        );

        return $actions;

    }

    /**
     * Processes bulk installation and activation actions.
     *
     * The bulk installation process looks either for the $_POST
     * information or for the plugin info within the $_GET variable if
     * a user has to use WP_Filesystem to enter their credentials.
     */
    public function process_bulk_actions()
    {

        // Bulk installation process.
        if ('tgmpa-bulk-install' === $this->current_action()) {
            check_admin_referer( 'bulk-' . $this->_args['plural'] );

            // Prep variables to be populated.
            $plugins_to_install = array();
            $plugin_installs = array();
            $plugin_path = array();
            $plugin_name = array();

            // Look first to see if information has been passed via WP_Filesystem.
            if (isset( $_GET['plugins'] )) {
                $plugins = explode( ',', stripslashes( $_GET['plugins'] ) );
            } // Looks like the user can use the direct method, take from $_POST.
            elseif (isset( $_POST['plugin'] )) {
                $plugins = (array) $_POST['plugin'];
            } // Nothing has been submitted.
            else {
                $plugins = array();
            }

            // Grab information from $_POST if available.
            if (isset( $_POST['plugin'] )) {
                foreach ($plugins as $plugin_data) {
                    $plugins_to_install[] = explode( ',', $plugin_data );
                }

                foreach ($plugins_to_install as $plugin_data) {
                    $plugin_installs[] = $plugin_data[0];
                    $plugin_path[] = $plugin_data[1];
                    $plugin_name[] = $plugin_data[2];
                }
            } // Information has been passed via $_GET.
            else {
                foreach ($plugins as $key => $value) {
                    // Grab plugin slug for each plugin.
                    if (0 == $key % 3 || 0 == $key) {
                        $plugins_to_install[] = $value;
                        $plugin_installs[] = $value;
                    }
                }
            }

            // Look first to see if information has been passed via WP_Filesystem.
            if (isset( $_GET['plugin_paths'] )) {
                $plugin_paths = explode( ',', stripslashes( $_GET['plugin_paths'] ) );
            } // Looks like the user doesn't need to enter his FTP creds.
            elseif (isset( $_POST['plugin'] )) {
                $plugin_paths = (array) $plugin_path;
            } // Nothing has been submitted.
            else {
                $plugin_paths = array();
            }

            // Look first to see if information has been passed via WP_Filesystem.
            if (isset( $_GET['plugin_names'] )) {
                $plugin_names = explode( ',', stripslashes( $_GET['plugin_names'] ) );
            } // Looks like the user doesn't need to enter his FTP creds.
            elseif (isset( $_POST['plugin'] )) {
                $plugin_names = (array) $plugin_name;
            } // Nothing has been submitted.
            else {
                $plugin_names = array();
            }

            // Loop through plugin slugs and remove already installed plugins from the list.
            $i = 0;
            foreach ($plugin_installs as $key => $plugin) {
                if (preg_match( '|.php$|', $plugin )) {
                    unset( $plugin_installs[$key] );

                    // If the plugin path isn't in the $_GET variable, we can unset the corresponding path.
                    if (!isset( $_GET['plugin_paths'] )) {
                        unset( $plugin_paths[$i] );
                    }

                    // If the plugin name isn't in the $_GET variable, we can unset the corresponding name.
                    if (!isset( $_GET['plugin_names'] )) {
                        unset( $plugin_names[$i] );
                    }
                }
                $i++;
            }

            // No need to proceed further if we have no plugins to install.
            if (empty( $plugin_installs )) {
                return false;
            }

            // Reset array indexes in case we removed already installed plugins.
            $plugin_installs = array_values( $plugin_installs );
            $plugin_paths = array_values( $plugin_paths );
            $plugin_names = array_values( $plugin_names );

            // If we grabbed our plugin info from $_GET, we need to decode it for use.
            $plugin_installs = array_map( 'urldecode', $plugin_installs );
            $plugin_paths = array_map( 'urldecode', $plugin_paths );
            $plugin_names = array_map( 'urldecode', $plugin_names );

            // Pass all necessary information via URL if WP_Filesystem is needed.
            $url = wp_nonce_url(
                add_query_arg(
                    array(
                        'page'         => $this->userInterfaceManager->getOption('menu'),
                        'tgmpa-action' => 'install-selected',
                        'plugins'      => urlencode( implode( ',', $plugins ) ),
                        'plugin_paths' => urlencode( implode( ',', $plugin_paths ) ),
                        'plugin_names' => urlencode( implode( ',', $plugin_names ) ),
                    ),
                    network_admin_url( 'themes.php' )
                ),
                'bulk-plugins'
            );
            $method = ''; // Leave blank so WP_Filesystem can populate it as necessary.
            $fields = array( 'action', '_wp_http_referer', '_wpnonce' ); // Extra fields to pass to WP_Filesystem.

            if (false === ( $creds = request_filesystem_credentials( $url, $method, false, false, $fields ) )) {
                return true;
            }

            if (!\WP_Filesystem( $creds )) {
                request_filesystem_credentials( $url, $method, true, false, $fields ); // Setup WP_Filesystem.

                return true;
            }

            require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for plugins_api
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // Need for upgrade classes

            // Store all information in arrays since we are processing a bulk installation.
            $api = array();
            $sources = array();

            // Loop through each plugin to install and try to grab information from WordPress API, if not create 'tgmpa-empty' scalar.
            $i = 0;
            foreach ($plugin_installs as $plugin) {
                $api[$i] = plugins_api(
                    'plugin_information',
                    array( 'slug' => $plugin, 'fields' => array( 'sections' => false ) )
                ) ? plugins_api(
                    'plugin_information',
                    array( 'slug' => $plugin, 'fields' => array( 'sections' => false ) )
                ) : (object) $api[$i] = 'tgmpa-empty';
                $i++;
            }

            if (is_wp_error( $api )) {
                wp_die( $this->userInterfaceManager->getMessage('oops') . var_dump( $api ) );
            }

            // Capture download links from $api or set install link to pre-packaged/private repo.
            $i = 0;
            foreach ($api as $object) {
                $sources[$i] = isset( $object->download_link ) && 'repo' == $plugin_paths[$i] ? $object->download_link : $plugin_paths[$i];
                $i++;
            }

            // Finally, all the data is prepared to be sent to the installer.
            $url = add_query_arg( array( 'page' => $this->userInterfaceManager->getOption('menu') ), network_admin_url( 'themes.php' ) );
            $nonce = 'bulk-plugins';
            $names = $plugin_names;

            $skin = new BulkInstallerSkin(
                compact( 'url', 'nonce', 'names' ),
                $this->userInterfaceManager,
                $this->pluginManager
            );
            $this->bulkInstaller->setSkin( $skin );

            // Wrap the install process with the appropriate HTML.
            echo '<div class="tgmpa wrap">';
            echo '<h2>' . esc_html( get_admin_page_title() ) . '</h2>';
            // Process the bulk installation submissions.
            $this->bulkInstaller->bulk_install( $sources, $this->userInterfaceManager->getOption('is_automatic') );
            echo '</div>';

            return true;
        }

        // Bulk activation process.
        if ('tgmpa-bulk-activate' === $this->current_action()) {
            check_admin_referer( 'bulk-' . $this->_args['plural'] );

            // Grab plugin data from $_POST.
            $plugins = isset( $_POST['plugin'] ) ? (array) $_POST['plugin'] : array();
            $plugins_to_activate = array();

            // Split plugin value into array with plugin file path, plugin source and plugin name.
            foreach ($plugins as $i => $plugin) {
                $plugins_to_activate[] = explode( ',', $plugin );
            }

            foreach ($plugins_to_activate as $i => $array) {
                if (!preg_match( '|.php$|', $array[0] )) {
                    unset( $plugins_to_activate[$i] );
                }
            }

            // Return early if there are no plugins to activate.
            if (empty( $plugins_to_activate )) {
                return false;
            }

            $plugins = array();
            $plugin_names = array();

            foreach ($plugins_to_activate as $plugin_string) {
                $plugins[] = $plugin_string[0];
                $plugin_names[] = $plugin_string[2];
            }

            $count = count( $plugin_names ); // Count so we can use _n function.
            $last_plugin = array_pop( $plugin_names ); // Pop off last name to prep for readability.
            $imploded = empty( $plugin_names ) ? '<strong>' . $last_plugin . '</strong>' : '<strong>' . ( implode(
                        ', ',
                        $plugin_names
                    ) . '</strong> and <strong>' . $last_plugin . '</strong>.' );

            // Now we are good to go - let's start activating plugins.
            $activate = activate_plugins( $plugins );

            if (is_wp_error( $activate )) {
                echo '<div id="message" class="error"><p>' . $activate->get_error_message() . '</p></div>';
            } else {
                printf(
                    '<div id="message" class="updated"><p>%1$s %2$s</p></div>',
                    _n(
                        'The following plugin was activated successfully:',
                        'The following plugins were activated successfully:',
                        $count,
                        'tgmpa'
                    ),
                    $imploded
                );
            }

            // Update recently activated plugins option.
            $recent = (array) get_option( 'recently_activated' );

            foreach ($plugins as $plugin => $time) {
                if (isset( $recent[$plugin] )) {
                    unset( $recent[$plugin] );
                }
            }

            update_option( 'recently_activated', $recent );

            unset( $_POST ); // Reset the $_POST variable in case user wants to perform one action after another.
        }

        return true;
    }

    /**
     * Prepares all of our information to be outputted into a usable table.
     */
    public function prepare_items()
    {
        $columns = $this->get_columns(); // Get all necessary column information.
        $hidden = array(); // No columns to hide, but we must set as an array.
        $sortable = array(); // No reason to make sortable columns.
        $this->_column_headers = array( $columns, $hidden, $sortable ); // Get all necessary column headers.

        // Process our bulk actions here.
        $this->process_bulk_actions();

        // Store all of our plugin data into $items array so WP_List_Table can use it.
        $this->items = $this->_gather_plugin_data();

    }

}
