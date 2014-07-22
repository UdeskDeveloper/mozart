<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Option;

use Mozart\Component\Debug\SystemInfo;
use Mozart\Component\Form\Field\Typography;
use Mozart\Component\Option\Extension\ExtensionManager;
use Mozart\Component\Option\Section\SectionManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OptionBuilder
 * @package Mozart\Component\Option
 */
class OptionBuilder
{
    /**
     * @var array
     */
    private $wp_data = array();
    /**
     * @var string
     */
    private $page = '';
    /**
     * @var array
     */
    private $options = array(); // Option values
    /**
     * @var null
     */
    private $options_defaults = null; // Option defaults
    /**
     * @var array
     */
    private $localize_data = array(); // Information that needs to be localized
    /**
     * @var null
     */
    private $outputCSS = null; // CSS that get auto-appended to the header
    /**
     * @var array
     */
    private $params = array();

    /**
     * @var bool
     */
    private $show_hints = false;
    /**
     * @var array
     */
    private $hidden_perm_sections = array(); //  Hidden sections specified by 'permissions' arg.

    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var array
     */
    private $transients = array();
    /**
     * @var
     */
    private $transients_check;
    /**
     * @var string
     */
    private $compilerCSS;

    /**
     * @param ContainerInterface $container
     */
    public function __construct( ContainerInterface $container )
    {
        $this->container = $container;
    }

    /**
     * @param  array $params
     * @return bool
     */
    public function boot( $params = array() )
    {
        $this->params = array_merge( $this->getDefaultArgs(), $params );

        if (empty( $this->params['opt_name'] )) {
            return false;
        }

        if ($this->params['global_variable'] == "" && $this->params['global_variable'] !== false) {
            $this->params['global_variable'] = str_replace( '-', '_', $this->params['opt_name'] );
        }

        $this->getExtensionManager()->loadExtensions();

        $this->loadTranslations();

        // Grab database values
        $this->loadOptions();

        $this->getFieldManager()->init( $this );
        $this->getFontManager()->init( $this );
        $this->getValidator()->init( $this );

        // Display admin notices in dev_mode
        if (true == $this->params['dev_mode']) {
            $this->getDebugger()->init( $this );
        }

        $this->getImporter()->init( $this );

        return true;
    }


    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $param
     * @return mixed
     */
    public function getParam( $param )
    {
        return $this->params[$param];
    }

    /**
     * @return ExtensionManager
     */
    public function getExtensionManager()
    {
        return $this->container->get( 'mozart.option.extensionmanager' );
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->container->get( 'mozart.option.validator' );
    }

    /**
     * @return SectionManager
     */
    public function getSectionManager()
    {
        return $this->container->get( 'mozart.option.sectionmanager' );
    }

    /**
     * @return FontManager
     */
    public function getFontManager()
    {
        return $this->container->get( 'mozart.option.fontmanager' );
    }

    /**
     * @return Importer
     */
    public function getImporter()
    {
        return $this->container->get( 'mozart.option.importer' );
    }

    /**
     * @return Debugger
     */
    public function getDebugger()
    {
        return $this->container->get( 'mozart.option.debugger' );
    }

    /**
     * @return FieldManager
     */
    public function getFieldManager()
    {
        return $this->container->get( 'mozart.option.fieldmanager' );
    }

    public function getOutputCSS() {
        return $this->outputCSS;
    }

    public function addToOutputCSS($css) {
        $this->outputCSS .= $css;
    }

    public function getCompilerCSS() {
        return $this->compilerCSS;
    }

    public function addToCompilerCSS($css) {
        $this->compilerCSS .= $css;
    }

    /**
     * @return array
     */
    protected function getDefaultArgs()
    {
        return array(
            'opt_name'          => '',
            // Must be defined by theme/plugin
            'google_api_key'    => '',
            // Must be defined to add google fonts to the typography module
            'last_tab'          => '',
            // force a specific tab to always show on reload
            'menu_icon'         => '',
            // menu icon
            'menu_title'        => '',
            // menu title/text
            'page_icon'         => 'icon-themes',
            'page_title'        => '',
            // option page title
            'page_slug'         => '_options',
            'page_permissions'  => 'manage_options',
            'menu_type'         => 'menu',
            // ('menu'|'submenu')
            'page_parent'       => 'themes.php',
            // requires menu_type = 'submenu
            'page_priority'     => null,
            'allow_sub_menu'    => true,
            // allow submenus to be added if menu_type == menu
            'save_defaults'     => true,
            // Save defaults to the DB on it if empty
            'footer_credit'     => '',
            'async_typography'  => false,
            'class'             => '',
            // Class that gets appended to all redux-containers
            'admin_bar'         => true,
            // Show the panel pages on the admin bar
            'help_tabs'         => array(),
            'help_sidebar'      => '',
            'database'          => '',
            // possible: options, theme_mods, theme_mods_expanded, transient, network
            'customizer'        => false,
            // setting to true forces get_theme_mod_expanded
            'global_variable'   => '',
            // Changes global variable from $GLOBALS['YOUR_OPT_NAME'] to whatever you set here. false disables the global variable
            'output'            => true,
            // Dynamically generate CSS
            'compiler'          => true,
            // Initiate the compiler hook
            'output_tag'        => true,
            // Print Output Tag
            'transient_time'    => '',
            'default_show'      => false,
            // If true, it shows the default value
            'default_mark'      => '',
            // What to print by the field's title if the value shown is default
            'update_notice'     => true,
            // Recieve an update notice of new commits when in dev mode
            'disable_save_warn' => false,
            // Disable the save warn
            'open_expanded'     => false,
            // Start the panel fully expanded to start with
            'network_admin'     => false,
            // Enable network admin when using network database mode
            'network_sites'     => true,
            // Enable sites as well as admin when using network database mode

            'hide_reset'        => false,
            'hints'             => array(
                'icon'          => 'icon-question-sign',
                'icon_position' => 'right',
                'icon_color'    => 'lightgray',
                'icon_size'     => 'normal',
                'tip_style'     => array(
                    'color'   => 'light',
                    'shadow'  => true,
                    'rounded' => false,
                    'style'   => '',
                ),
                'tip_position'  => array(
                    'my' => 'top_left',
                    'at' => 'bottom_right',
                ),
                'tip_effect'    => array(
                    'show' => array(
                        'effect'   => 'slide',
                        'duration' => '500',
                        'event'    => 'mouseover',
                    ),
                    'hide' => array(
                        'effect'   => 'fade',
                        'duration' => '500',
                        'event'    => 'click mouseleave',
                    ),
                ),
            ),
            'show_importer'     => true,
            'dev_mode'          => false,
            'system_info'       => false
        );
    }

    /**
     * @param \WP_Admin_Bar $wp_admin_bar
     */
    public function adminBarMenuForNetwork( \WP_Admin_Bar $wp_admin_bar )
    {
        $params = array(
            'id'     => $this->params['opt_name'] . '_network_admin',
            'title'  => $this->params['menu_title'],
            'parent' => 'network-admin',
            'href'   => network_admin_url( 'settings.php' ) . '?page=' . $this->params['page_slug'],
            'meta'   => array( 'class' => 'redux-network-admin' )
        );
        $wp_admin_bar->add_node( $params );

    }

    /**
     * @param $value
     *
     * @return array|string
     */
    public function stripslashes_deep( $value )
    {
        $value = is_array( $value ) ?
            array_map( 'stripslashes_deep', $value ) :
            stripslashes( $value );

        return $value;
    }

    /**
     *
     */
    public function save_network_page()
    {
        $data = $this->_validate_options( $_POST[$this->params['opt_name']] );

        if (!empty( $data )) {
            $this->setOptions( $data );
        }

        wp_redirect(
            add_query_arg(
                array(
                    'page'    => $this->params['page_slug'],
                    'updated' => 'true'
                ),
                network_admin_url( 'settings.php' )
            )
        );
        exit();
    }

    /**
     * load translations
     */
    private function loadTranslations()
    {
        $locale = get_locale();

        if (strpos( $locale, '_' ) === false) {
            if (file_exists(
                $this->container->getParameter( 'wp.plugin.dir' ) . '/mozart' . '/translations/option/' . strtolower(
                    $locale
                ) . '_' . strtoupper( $locale ) . '.mo'
            )) {
                $locale = strtolower( $locale ) . '_' . strtoupper( $locale );
            }
        }
        load_textdomain(
            'mozart-options',
            $this->container->getParameter( 'wp.plugin.dir' ) . '/mozart' . '/translations/option/' . $locale . '.mo'
        );
    }

    /**
     * This is used to return the default value if default_show is set
     *
     * @param string $opt_name The option name to return
     * @param mixed $default (null)  The value to return if default not set
     *
     * @return mixed $default
     */
    public function getDefaultOption( $opt_name, $default = null )
    {
        if ($this->params['default_show'] == true) {

            if (empty( $this->options_defaults )) {
                $this->getDefaultOptions(); // fill cache
            }

            $default = array_key_exists(
                $opt_name,
                $this->options_defaults
            ) ? $this->options_defaults[$opt_name] : $default;
        }

        return $default;
    }

    /**
     * This is used to return and option value from the options array
     *
     * @param string $opt_name The option name to return
     * @param mixed $default (null) The value to return if option not set
     *
     * @return mixed
     */
    public function getOption( $opt_name, $default = null )
    {
        return ( !empty( $this->options[$opt_name] ) ) ? $this->options[$opt_name] : $this->getDefaultOption(
            $opt_name,
            $default
        );
    }

    /**
     * This is used to set an arbitrary option in the options array
     *
     * @param string $opt_name The name of the option being added
     * @param mixed $value The value of the option being added
     *
     * @return void
     */
    public function setOption( $opt_name = '', $value = '' )
    {
        if ($opt_name != '') {
            $this->options[$opt_name] = $value;
            $this->setOptions( $this->options );
        }
    }

    /**
     * Set a global variable by the global_variable argument
     *
     * @return bool
     */
    public function setGlobalVariable()
    {
        if ($this->params['global_variable']) {
            $option_global = $this->params['global_variable'];

            if (isset( $this->transients['last_save'] )) {
                // Deprecated
                $GLOBALS[$option_global]['REDUX_last_saved'] = $this->transients['last_save'];
                // Last save key
                $GLOBALS[$option_global]['REDUX_LAST_SAVE'] = $this->transients['last_save'];
            }
            if (isset( $this->transients['last_compiler'] )) {
                // Deprecated
                $GLOBALS[$option_global]['REDUX_COMPILER'] = $this->transients['last_compiler'];
                // Last compiler hook key
                $GLOBALS[$option_global]['REDUX_LAST_COMPILER'] = $this->transients['last_compiler'];
            }

            return true;
        }

        return false;
    }

    /**
     * This is used to set an arbitrary option in the options array
     *
     * @param mixed $value the value of the option being added
     */
    public function setOptions( $value = '' )
    {
        $this->transients['last_save'] = time();

        if (!empty( $value )) {
            $this->options = $value;

            if ($this->params['database'] === 'transient') {
                set_transient( $this->params['opt_name'] . '-transient', $value, $this->params['transient_time'] );
            } elseif ($this->params['database'] === 'theme_mods') {
                set_theme_mod( $this->params['opt_name'] . '-mods', $value );
            } elseif ($this->params['database'] === 'theme_mods_expanded') {
                foreach ($value as $k => $v) {
                    set_theme_mod( $k, $v );
                }
            } elseif ($this->params['database'] === 'network') {
                // Strip those slashes!
                $value = json_decode( stripslashes( json_encode( $value ) ), true );
                update_site_option( $this->params['opt_name'], $value );
            } else {
                update_option( $this->params['opt_name'], $value );
            }

            // Store the changed values in the transient
            if ($value != $this->options) {
                foreach ($value as $k => $v) {
                    if (!isset( $this->options[$k] )) {
                        $this->options[$k] = "";
                    } elseif ($v == $this->options[$k]) {
                        unset( $this->options[$k] );
                    }
                }
                $this->transients['changed_values'] = $this->options;
            }

            $this->options = $value;

            // Set a global variable by the global_variable argument.
            $this->setGlobalVariable();

            // Saving the transient values
            $this->setTransients();
        }
    }

    /**
     * This is used to get options from the database
     *
     */
    public function loadOptions()
    {
        $defaults = false;

        if (!empty( $this->defaults )) {
            $defaults = $this->defaults;
        }

        $options = $this->getOptions();

        if (empty( $options ) && !empty( $defaults )) {
            $this->setOptions( $defaults );
        } else {
            $this->options = $options;
        }

        // Get transient values
        $this->getTransients();

        // Set a global variable by the global_variable argument.
        $this->setGlobalVariable();
    }

    /**
     * @return array|mixed|string|void
     */
    public function getOptions()
    {
        if ($this->params['database'] === "transient") {
            return get_transient( $this->params['opt_name'] . '-transient' );
        } elseif ($this->params['database'] === "theme_mods") {
            return get_theme_mod( $this->params['opt_name'] . '-mods' );
        } elseif ($this->params['database'] === 'theme_mods_expanded') {
            return get_theme_mods();
        } elseif ($this->params['database'] === 'network') {
            return get_site_option( $this->params['opt_name'], array() );
//            return json_decode( stripslashes( json_encode( $result ) ), true );
        }

        return get_option( $this->params['opt_name'], array() );
    }

    /**
     * Get Wordpress specific data from the DB and return in a usable array
     * @param  bool $type
     * @param  array $params
     * @return array|string
     */
    public function get_wordpress_data( $type = false, $params = array() )
    {
        $data = "";

        $paramsKey = "";
        foreach ($params as $key => $value) {
            if (!is_array( $value )) {
                $paramsKey .= $value . "-";
            } else {
                $paramsKey .= implode( "-", $value );
            }
        }

        if (empty( $data ) && isset( $this->wp_data[$type . $paramsKey] )) {
            $data = $this->wp_data[$type . $paramsKey];
        }

        if (empty( $data ) && !empty( $type )) {

            /**
             * Use data from Wordpress to populate options array
             **/
            if (!empty( $type ) && empty( $data )) {
                $data = array();

                switch ($type) {
                    case "categories":
                    case "category":
                        $cats = get_categories( $params );
                        foreach ((array)$cats as $cat) {
                            $data[$cat->term_id] = $cat->name;
                        }
                        break;
                    case "menus":
                    case "menu":
                        $menus = wp_get_nav_menus( $params );
                        foreach ((array)$menus as $item) {
                            $data[$item->term_id] = $item->name;
                        }
                        break;
                    case "pages":
                    case "page":
                        if (!isset( $params['posts_per_page'] )) {
                            $params['posts_per_page'] = 20;
                        }
                        $pages = get_pages( $params );
                        foreach ((array)$pages as $page) {
                            $data[$page->ID] = $page->post_title;
                        }
                        break;
                    case "terms":
                    case "term":
                        $taxonomies = $params['taxonomies'];
                        unset( $params['taxonomies'] );

                        $terms = get_terms( $taxonomies, $params ); // this will get nothing
                        foreach ((array)$terms as $term) {
                            $data[$term->term_id] = $term->name;
                        }
                        break;
                    case "taxonomy":
                    case "taxonomies":
                        $taxonomies = get_taxonomies( $params );
                        foreach ((array)$taxonomies as $key => $taxonomy) {
                            $data[$key] = $taxonomy;
                        }
                        break;
                    case "posts":
                    case "post":
                        $posts = get_posts( $params );
                        foreach ((array)$posts as $post) {
                            $data[$post->ID] = $post->post_title;
                        }
                        break;
                    case "post_type":
                    case "post_types":
                        global $wp_post_types;

                        $defaults = array(
                            'public'              => true,
                            'exclude_from_search' => false,
                        );
                        $params = array_merge( $defaults, $params );

                        $output = 'names';
                        $operator = 'and';
                        $post_types = get_post_types( $params, $output, $operator );

                        ksort( $post_types );

                        foreach ($post_types as $name => $title) {
                            if (isset( $wp_post_types[$name]->labels->menu_name )) {
                                $data[$name] = $wp_post_types[$name]->labels->menu_name;
                            } else {
                                $data[$name] = ucfirst( $name );
                            }
                        }
                        break;
                    case "tags":
                    case "tag": // NOT WORKING!
                        $tags = get_tags( $params );
                        foreach ((array)$tags as $tag) {
                            $data[$tag->term_id] = $tag->name;
                        }
                        break;
                    case "menu_location":
                    case "menu_locations":
                        global $_wp_registered_nav_menus;

                        foreach ($_wp_registered_nav_menus as $k => $v) {
                            $data[$k] = $v;
                        }
                        break;
                    case "elusive-icons":
                    case "elusive-icon":
                    case "elusive":
                    case "font-icon":
                    case "font-icons":
                    case "icons":
                        foreach ($this->getFontManager()->getFontIcons() as $k) {
                            $data[$k] = $k;
                        }
                        break;
                    case "roles":
                        /** @global \WP_Roles $wp_roles */
                        global $wp_roles;

                        $data = $wp_roles->get_names();
                        break;
                    case "sidebars":
                    case "sidebar":
                        /** @global array $wp_registered_sidebars */
                        global $wp_registered_sidebars;

                        foreach ($wp_registered_sidebars as $key => $value) {
                            $data[$key] = $value['name'];
                        }
                        break;
                    case "capabilities":
                        /** @global \WP_Roles $wp_roles */
                        global $wp_roles;

                        foreach ($wp_roles->roles as $role) {
                            foreach ($role['capabilities'] as $key => $cap) {
                                $data[$key] = ucwords( str_replace( '_', ' ', $key ) );
                            }
                        }
                        break;
                    case "callback":
                        if (!is_array( $params )) {
                            $params = array( $params );
                        }
                        $data = call_user_func( $params[0] );
                        break;
                }
            }
            $this->wp_data[$type . $paramsKey] = $data;
        }

        return $data;
    }

    /**
     * This is used to echo and option value from the options array
     *
     * @param string $opt_name The name of the option being shown
     * @param mixed $default The value to show if $opt_name isn't set
     *
     * @return void
     */
    public function show( $opt_name, $default = '' )
    {
        $option = $this->getOption( $opt_name );
        if (!is_array( $option ) && $option != '') {
            echo $option;
        } elseif ($default != '') {
            echo $this->getDefaultOption( $opt_name, $default );
        }
    }

    /**
     * Get default options into an array suitable for the settings API
     *
     * @return array
     */
    private function getDefaultOptions()
    {
        if (!empty( $this->options_defaults )) {
            return $this->options_defaults;
        }

        // fill the cache
        foreach ($this->getSectionManager()->getSections() as $alias => $section) {
            if (!isset( $section['fields'] )) {
                continue;
            }

            foreach ($section['fields'] as $k => $field) {
                if (empty( $field['id'] ) && empty( $field['type'] )) {
                    continue;
                }

                $this->getFieldManager()->addField( $field );

                if (isset( $field['default'] )) {
                    $this->options_defaults[$field['id']] = $field['default'];
                } elseif (isset( $field['options'] )) {
                    // Sorter data filter
                    if ($field['type'] == "sorter" && isset( $field['data'] ) && !empty( $field['data'] ) && is_array(
                            $field['data']
                        )
                    ) {
                        if (!isset( $field['params'] )) {
                            $field['params'] = array();
                        }
                        foreach ($field['data'] as $key => $data) {
                            if (!isset( $field['params'][$key] )) {
                                $field['params'][$key] = array();
                            }
                            $field['options'][$key] = $this->get_wordpress_data(
                                $data,
                                $field['params'][$key]
                            );
                        }
                    }
                    $this->options_defaults[$field['id']] = $field['options'];
                }
            }
        }

        return $this->options_defaults;
    }


    /**
     * Class Add Sub Menu Function, creates options submenu in Wordpress admin area.
     *
     * @param $page_parent
     * @param $page_title
     * @param $menu_title
     * @param $page_permissions
     * @param $page_slug
     * @return void
     */
    private function add_submenu( $page_parent, $page_title, $menu_title, $page_permissions, $page_slug )
    {
        global $submenu;

        // Just in case. One never knows.
        $page_parent = strtolower( $page_parent );

        $test = array(
            'index.php'               => 'dashboard',
            'edit.php'                => 'posts',
            'upload.php'              => 'media',
            'link-manager.php'        => 'links',
            'edit.php?post_type=page' => 'pages',
            'edit-comments.php'       => 'comments',
            'themes.php'              => 'theme',
            'plugins.php'             => 'plugins',
            'users.php'               => 'users',
            'tools.php'               => 'management',
            'options-general.php'     => 'options',
        );

        if (isset( $test[$page_parent] )) {
            $function = 'add_' . $test[$page_parent] . '_page';
            $this->page = $function(
                $page_title,
                $menu_title,
                $page_permissions,
                $page_slug,
                array( $this, '_options_page_html' )
            );
        } else {
            // Network settings and Post type menus. These do not have
            // wrappers and need to be appened to using add_submenu_page.
            // Okay, since we've left the post type menu appending
            // as default, we need to validate it, so anything that
            // isn't post_type=<post_type> doesn't get through and mess
            // things up.
            $addMenu = false;
            if ('settings.php' != $page_parent) {
                // Establish the needle
                $needle = '?post_type=';

                // Check if it exists in the page_parent (how I miss instr)
                $needlePos = strrpos( $page_parent, $needle );

                // It's there, so...
                if ($needlePos > 0) {

                    // Get the post type.
                    $postType = substr( $page_parent, $needlePos + strlen( $needle ) );

                    // Ensure it exists.
                    if (post_type_exists( $postType )) {
                        // Set flag to add the menu page
                        $addMenu = true;
                    }
                    // custom menu
                } elseif (isset( $submenu[$this->params['page_parent']] )) {
                    $addMenu = true;
                }

            } else {
                // The page_parent was settings.php, so set menu add
                // flag to true.
                $addMenu = true;
            }
            // Add the submenu if it's permitted.
            if (true == $addMenu) {
                $this->page = add_submenu_page(
                    $page_parent,
                    $page_title,
                    $menu_title,
                    $page_permissions,
                    $page_slug,
                    array(
                        &$this,
                        '_options_page_html'
                    )
                );
            }
        }
    }

    /**
     * Class Options Page Function, creates main options page.
     *
     * @return void
     */
    public function _options_page()
    {
        $this->getImporter()->checkEnabled();

        if ($this->params['menu_type'] == 'submenu') {
            $this->add_submenu(
                $this->params['page_parent'],
                $this->params['page_title'],
                $this->params['menu_title'],
                $this->params['page_permissions'],
                $this->params['page_slug']
            );

        } else {
            $this->page = add_menu_page(
                $this->params['page_title'],
                $this->params['menu_title'],
                $this->params['page_permissions'],
                $this->params['page_slug'],
                array( &$this, '_options_page_html' ),
                $this->params['menu_icon'],
                $this->params['page_priority']
            );

            if (true === $this->params['allow_sub_menu']) {

                $this->getSectionManager()->addSubmenuPages($this->params['page_slug'], $this->params['page_permissions']);

                if (true == $this->params['show_importer'] && false == $this->getImporter()->isEnabled()
                ) {
                    $this->getImporter()->add_submenu();
                }

                if (true == $this->params['dev_mode']) {
                    $this->getDebugger()->add_submenu();
                }

                if (true == $this->params['system_info']) {
                    add_submenu_page(
                        $this->params['page_slug'],
                        __( 'System Info', 'mozart-options' ),
                        __( 'System Info', 'mozart-options' ),
                        $this->params['page_permissions'],
                        $this->params['page_slug'] . '&tab=system_info_default',
                        '__return_null'
                    );
                }
            }
        }

        add_action( "load-{$this->page}", array( &$this, '_load_page' ) );
    }

    /**
     * Add admin bar menu
     *
     * @return void
     */
    public function adminBarMenu()
    {
        global /** @var \WP_Admin_Bar $wp_admin_bar */
        $menu, $submenu, $wp_admin_bar;

        $theme_data = wp_get_theme();

        if (!is_super_admin() || !is_admin_bar_showing() || !$this->params['admin_bar']) {
            return;
        }

        if ($menu) {
            foreach ($menu as $menu_item) {
                if (isset( $menu_item[2] ) && $menu_item[2] === $this->params["page_slug"]) {
                    $nodeparams = array(
                        'id'    => $menu_item[2],
                        'title' => "<span class='ab-icon dashicons-admin-generic'></span>" . $menu_item[0],
                        'href'  => admin_url( 'admin.php?page=' . $menu_item[2] ),
                        'meta'  => array()
                    );
                    $wp_admin_bar->add_node( $nodeparams );

                    break;
                }
            }

            if (isset( $submenu[$this->params["page_slug"]] ) && is_array( $submenu[$this->params["page_slug"]] )) {
                foreach ($submenu[$this->params["page_slug"]] as $index => $redux_options_submenu) {
                    $subnodeparams = array(
                        'id'     => $this->params["page_slug"] . '_' . $index,
                        'title'  => $redux_options_submenu[0],
                        'parent' => $this->params["page_slug"],
                        'href'   => admin_url( 'admin.php?page=' . $redux_options_submenu[2] ),
                    );

                    $wp_admin_bar->add_node( $subnodeparams );
                }
            }
        } else {
            $nodeparams = array(
                'id'    => $this->params["page_slug"],
                'title' => "<span class='ab-icon dashicons-admin-generic'></span>" . $theme_data->get(
                        'Name'
                    ) . " " . __( 'Options', 'mozart-options-demo' ),
                'href'  => admin_url( 'admin.php?page=' . $this->params["page_slug"] ),
                'meta'  => array()
            );

            $wp_admin_bar->add_node( $nodeparams );
        }
    }

    /**
     * Output dynamic CSS at bottom of HEAD
     *
     * @return void
     */
    public function _output_css()
    {
        if ($this->params['output'] == false && $this->params['compiler'] == false) {
            return;
        }

        if (isset( $this->no_output )) {
            return;
        }

        if (!empty( $this->outputCSS ) && ( $this->params['output_tag'] == true || ( isset( $_POST['customized'] ) ) )) {
            echo '<style type="text/css" title="dynamic-css" class="options-output">' . $this->outputCSS . '</style>';
        }
    }

    /**
     * Enqueue CSS and Google fonts for front end
     *
     * @return void
     */
    public function enqueueScriptsOutput()
    {
        if ($this->params['output'] == false && $this->params['compiler'] == false) {
            return;
        }

        foreach ($this->getSectionManager()->getSections() as $k => $section) {
            if (isset( $section['type'] ) && ( $section['type'] == 'divide' )) {
                continue;
            }

            if (!isset( $section['fields'] )) {
                continue;
            }
            foreach ($section['fields'] as $fieldk => $field) {
                $this->getFieldManager()->enqueueOutput( $field );
            }
        }

        // For use like in the customizer. Stops the output, but passes the CSS in the variable for the compiler
        if (isset( $this->no_output )) {
            return;
        }
//        $this->getFontManager()->enqueueTypographyFonts();
    }

    public function getLastSave() {
        return !empty( $this->transients['last_save'] ) ? $this->transients['last_save'] : '';
    }

    /**
     * Enqueue CSS/JS for options page
     * @return void
     */
    public function adminEnqueueScripts()
    {
        global $wp_styles;

        if ($this->getImporter()->isEnabled()) {

            wp_enqueue_script(
                'redux-field-import-export-js',
                $this->container->getParameter(
                    'wp.plugin.uri'
                ) . '/mozart' . '/public/bundles/mozart/option/js/import_export/import_export.js',
                array( 'jquery', 'redux-js' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-import-export-css',
                $this->container->getParameter(
                    'wp.plugin.uri'
                ) . '/mozart' . '/public/bundles/mozart/option/css/import_export/import_export.css',
                time(),
                true
            );
        }


        if ($this->isFieldInUseByType(
            $this->getFieldManager()->getFields(),
            array(
                'background',
                'border',
                'dimensions',
                'select',
                'select_image',
                'slider',
                'spacing',
                'typography',
                'color_scheme'

            )
        )
        ) {

            // select2 CSS
            wp_register_style(
                'select2-css',
                $this->container->getParameter(
                    'wp.plugin.uri'
                ) . '/mozart' . '/public/bundles/mozart/option/js/vendor/select2/select2.css',
                array(),
                filemtime(
                    $this->container->getParameter(
                        'wp.plugin.dir'
                    ) . '/mozart' . '/public/bundles/mozart/option/js/vendor/select2/select2.css'
                ),
                'all'
            );

            wp_enqueue_style( 'select2-css' );

            // JS
            wp_register_script(
                'select2-sortable-js',
                $this->container->getParameter(
                    'wp.plugin.uri'
                ) . '/mozart' . '/public/bundles/mozart/option/js/vendor/select2.sortable.min.js',
                array( 'jquery' ),
                filemtime(
                    $this->container->getParameter(
                        'wp.plugin.dir'
                    ) . '/mozart' . '/public/bundles/mozart/option/js/vendor/select2.sortable.min.js'
                ),
                true
            );

            wp_register_script(
                'select2-js',
                $this->container->getParameter(
                    'wp.plugin.uri'
                ) . '/mozart' . '/public/bundles/mozart/option/js/vendor/select2/select2.min.js',
                array( 'jquery', 'select2-sortable-js' ),
                filemtime(
                    $this->container->getParameter(
                        'wp.plugin.dir'
                    ) . '/mozart' . '/public/bundles/mozart/option/js/vendor/select2/select2.min.js'
                ),
                true
            );

            wp_enqueue_script( 'select2-js' );
        }

        wp_register_style(
            'redux-css',
            $this->container->getParameter(
                'wp.plugin.uri'
            ) . '/mozart' . '/public/bundles/mozart/option/css/redux.css',
            array( 'farbtastic' ),
            filemtime(
                $this->container->getParameter(
                    'wp.plugin.dir'
                ) . '/mozart' . '/public/bundles/mozart/option/css/redux.css'
            ),
            'all'
        );

        wp_register_style(
            'admin-css',
            $this->container->getParameter(
                'wp.plugin.uri'
            ) . '/mozart' . '/public/bundles/mozart/option/css/admin.css',
            array( 'farbtastic' ),
            filemtime(
                $this->container->getParameter(
                    'wp.plugin.dir'
                ) . '/mozart' . '/public/bundles/mozart/option/css/admin.css'
            ),
            'all'
        );

        wp_register_style(
            'redux-elusive-icon',
            $this->container->getParameter(
                'wp.plugin.uri'
            ) . '/mozart' . '/public/bundles/mozart/option/css/vendor/elusive-icons/elusive-webfont.css',
            array(),
            filemtime(
                $this->container->getParameter(
                    'wp.plugin.dir'
                ) . '/mozart' . '/public/bundles/mozart/option/css/vendor/elusive-icons/elusive-webfont.css'
            ),
            'all'
        );

        wp_register_style(
            'redux-elusive-icon-ie7',
            $this->container->getParameter(
                'wp.plugin.uri'
            ) . '/mozart' . '/public/bundles/mozart/option/css/vendor/elusive-icons/elusive-webfont-ie7.css',
            array(),
            filemtime(
                $this->container->getParameter(
                    'wp.plugin.dir'
                ) . '/mozart' . '/public/bundles/mozart/option/css/vendor/elusive-icons/elusive-webfont-ie7.css'
            ),
            'all'
        );

        wp_register_style(
            'qtip-css',
            $this->container->getParameter(
                'wp.plugin.uri'
            ) . '/mozart' . '/public/bundles/mozart/option/css/vendor/qtip/jquery.qtip.css',
            array(),
            filemtime(
                $this->container->getParameter(
                    'wp.plugin.dir'
                ) . '/mozart' . '/public/bundles/mozart/option/css/vendor/qtip/jquery.qtip.css'
            ),
            'all'
        );

        $wp_styles->add_data( 'redux-elusive-icon-ie7', 'conditional', 'lte IE 7' );

        wp_register_style(
            'jquery-ui-css',
            $this->container->getParameter(
                'wp.plugin.uri'
            ) . '/mozart' . '/public/bundles/mozart/option/css/vendor/jquery-ui-bootstrap/jquery-ui-1.10.0.custom.css'
            ,
            '',
            filemtime(
                $this->container->getParameter(
                    'wp.plugin.dir'
                ) . '/mozart' . '/public/bundles/mozart/option/css/vendor/jquery-ui-bootstrap/jquery-ui-1.10.0.custom.css'
            ),
            'all'
        );

        wp_enqueue_style( 'jquery-ui-css' );
        wp_enqueue_style( 'redux-lte-ie8' );
        wp_enqueue_style( 'qtip-css' );
        wp_enqueue_style( 'redux-elusive-icon' );
        wp_enqueue_style( 'redux-elusive-icon-ie7' );

        if (is_rtl()) {
            wp_register_style(
                'redux-rtl-css',
                $this->container->getParameter(
                    'wp.plugin.uri'
                ) . '/mozart' . '/public/bundles/mozart/option/css/rtl.css',
                '',
                filemtime(
                    $this->container->getParameter(
                        'wp.plugin.dir'
                    ) . '/mozart' . '/public/bundles/mozart/option/css/rtl.css'
                ),
                'all'
            );
            wp_enqueue_style( 'redux-rtl-css' );
        }

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-dialog' );

        // Load jQuery sortable for slides, sorter, sortable and group
        if ($this->isFieldInUseByType(
            $this->getFieldManager()->getFields(),
            array(
                'slides',
                'sorter',
                'sortable',
                'group'
            )
        )
        ) {
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_style( 'jquery-ui-sortable' );
        }

        // Load jQuery UI Datepicker for date
        if ($this->isFieldInUseByType( $this->getFieldManager()->getFields(), array( 'date' ) )) {
            wp_enqueue_script( 'jquery-ui-datepicker' );
        }

        // Load jQuery UI Accordion for slides and group
        if ($this->isFieldInUseByType( $this->getFieldManager()->getFields(), array( 'slides', 'group' ) )) {
            wp_enqueue_script( 'jquery-ui-accordion' );
        }

        // Load wp-color-picker for color, color_gradient, link_color, border, background and typography
        if ($this->isFieldInUseByType(
            $this->getFieldManager()->getFields(),
            array(
                'background',
                'color',
                'color_gradient',
                'link_color',
                'border',
                'typography'
            )
        )
        ) {

            wp_register_style(
                'color-picker-css',
                $this->container->getParameter(
                    'wp.plugin.uri'
                ) . '/mozart' . '/public/bundles/mozart/option/css/color-picker/color-picker.css',
                array(),
                filemtime(
                    $this->container->getParameter(
                        'wp.plugin.dir'
                    ) . '/mozart' . '/public/bundles/mozart/option/css/color-picker/color-picker.css'
                ),
                'all'
            );

            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_style( 'wp-color-picker' );
        }

        if (function_exists( 'wp_enqueue_media' )) {
            wp_enqueue_media();
        } else {
            wp_enqueue_script( 'media-upload' );
        }

        add_thickbox();

        wp_register_script(
            'qtip-js',
            $this->container->getParameter(
                'wp.plugin.uri'
            ) . '/mozart' . '/public/bundles/mozart/option/js/vendor/qtip/jquery.qtip.js',
            array( 'jquery' ),
            '2.2.0',
            true
        );

        wp_register_script(
            'serializeForm-js',
            $this->container->getParameter(
                'wp.plugin.uri'
            ) . '/mozart' . '/public/bundles/mozart/option/js/vendor/jquery.serializeForm.js',
            array( 'jquery' ),
            '1.0.0',
            true
        );

        // Embed the compress version unless in dev mode
        // dev_mode = true
        if (isset( $this->params['dev_mode'] ) && $this->params['dev_mode'] == true) {
            wp_enqueue_style( 'admin-css' );
            wp_register_script(
                'redux-vendor',
                $this->container->getParameter(
                    'wp.plugin.uri'
                ) . '/mozart' . '/public/bundles/mozart/option/js/vendor.min.js',
                array( 'jquery' ),
                filemtime(
                    $this->container->getParameter(
                        'wp.plugin.dir'
                    ) . '/mozart' . '/public/bundles/mozart/option/js/vendor.min.js'
                ),
                true
            );

            // dev_mode - false
        } else {
            wp_enqueue_style( 'redux-css' );
        }

        $depArray = array( 'jquery', 'qtip-js', 'serializeForm-js', );

        if (true == $this->params['dev_mode']) {
            array_push( $depArray, 'redux-vendor' );
        }

        wp_register_script(
            'redux-js',
            $this->container->getParameter( 'wp.plugin.uri' ) . '/mozart' . '/public/bundles/mozart/option/js/redux.js',
            $depArray,
            filemtime(
                $this->container->getParameter(
                    'wp.plugin.dir'
                ) . '/mozart' . '/public/bundles/mozart/option/js/redux.js'
            ),
            true
        );

        foreach ($this->getSectionManager()->getSections() as $section) {
            if (!isset( $section['fields'] )) {
                continue;
            }
            foreach ($section['fields'] as $field) {
                $this->getFieldManager()->enqueueScripts($field);
                $this->localize_data = $this->getFieldManager()->localizeFieldData($field, $this->localize_data);
            }
        }

        $this->localize_data = $this->getFieldManager()->addLocalizeData($this->localize_data);
        $this->localize_data = $this->getFontManager()->addLocalizeData($this->localize_data);

        $this->localize_data['options'] = $this->options;
        $this->localize_data['defaults'] = $this->options_defaults;

        $save_pending = __( 'You have changes that are not saved. Would you like to save them now?', 'mozart-options' );
        $reset_all = __( 'Are you sure? Resetting will lose all custom values.', 'mozart-options' );
        $reset_section = __( 'Are you sure? Resetting will lose all custom values in this section.', 'mozart-options' );
        $preset_confirm = __(
            'Your current options will be replaced with the values of this preset. Would you like to proceed?',
            'mozart-options'
        );

        $this->localize_data['params'] = array(
            'save_pending'          => $save_pending,
            'reset_confirm'         => $reset_all,
            'reset_section_confirm' => $reset_section,
            'preset_confirm'        => $preset_confirm,
            'please_wait'           => __( 'Please Wait', 'mozart-options' ),
            'opt_name'              => $this->params['opt_name'],
            'slug'                  => $this->params['page_slug'],
            'hints'                 => $this->params['hints'],
            'disable_save_warn'     => $this->params['disable_save_warn'],
            'class'                 => $this->params['class'],
        );

        // Construct the errors array.
        if (isset( $this->transients['last_save_mode'] ) && !empty( $this->transients['notices']['errors'] )) {
            $theTotal = 0;
            $theErrors = array();

            foreach ($this->transients['notices']['errors'] as $error) {
                $theErrors[$error['section_id']]['errors'][] = $error;

                if (!isset( $theErrors[$error['section_id']]['total'] )) {
                    $theErrors[$error['section_id']]['total'] = 0;
                }

                $theErrors[$error['section_id']]['total']++;
                $theTotal++;
            }

            $this->localize_data['errors'] = array( 'total' => $theTotal, 'errors' => $theErrors );
            unset( $this->transients['notices']['errors'] );
        }

        // Construct the warnings array.
        if (isset( $this->transients['last_save_mode'] ) && !empty( $this->transients['notices']['warnings'] )) {
            $theTotal = 0;
            $theWarnings = array();

            foreach ($this->transients['notices']['warnings'] as $warning) {
                $theWarnings[$warning['section_id']]['warnings'][] = $warning;

                if (!isset( $theWarnings[$warning['section_id']]['total'] )) {
                    $theWarnings[$warning['section_id']]['total'] = 0;
                }

                $theWarnings[$warning['section_id']]['total']++;
                $theTotal++;
            }

            unset( $this->transients['notices']['warnings'] );
            $this->localize_data['warnings'] = array( 'total' => $theTotal, 'warnings' => $theWarnings );
        }

        if (empty( $this->transients['notices'] )) {
            unset( $this->transients['notices'] );
        }

        // Values used by the javascript
        wp_localize_script(
            'redux-js',
            'redux',
            $this->localize_data
        );

        wp_enqueue_script( 'redux-js' ); // Enque the JS now

        wp_enqueue_script(
            'webfontloader',
            'https://ajax.googleapis.com/ajax/libs/webfont/1.5.0/webfont.js',
            array( 'jquery' ),
            '1.5.0',
            true
        );
    }

    public function addLocalizeData($key, $value) {

    }

    /**
     * Show page help
     * @return void
     */
    public function _load_page()
    {
        // Do admin footer text hook
        add_filter( 'admin_footer_text', array( &$this, 'admin_footer_text' ) );

        $screen = get_current_screen();

        if (is_array( $this->params['help_tabs'] )) {
            foreach ($this->params['help_tabs'] as $tab) {
                $screen->add_help_tab( $tab );
            }
        }

        // If hint argument is set, display hint tab
        if (true == $this->show_hints) {
            global $current_user;

            // Users enable/disable hint choice
            $hint_status = get_user_meta( $current_user->ID, 'ignore_hints' ) ? get_user_meta(
                $current_user->ID,
                'ignore_hints',
                true
            ) : 'true';

            // current page parameters
            $curPage = $_GET['page'];

            $curTab = '0';
            if (isset( $_GET['tab'] )) {
                $curTab = $_GET['tab'];
            }

            // Default url values for enabling hints.
            $dismiss = 'true';
            $s = 'Enable';

            // Values for disabling hints.
            if ('true' == $hint_status) {
                $dismiss = 'false';
                $s = 'Disable';
            }

            // Make URL
            $url = '<a class="redux_hint_status" href="?dismiss=' . $dismiss . '&amp;id=hints&amp;page=' . $curPage . '&amp;tab=' . $curTab . '">' . $s . ' hints</a>';

            $event = 'moving the mouse over';
            if ('click' == $this->params['hints']['tip_effect']['show']['event']) {
                $event = 'clicking';
            }

            // Construct message
            $msg = 'Hints are tooltips that popup when ' . $event . ' the hint icon, offering addition information about the field in which they appear.  They can be ' . strtolower(
                    $s
                ) . 'd by using the link below.<br/><br/>' . $url;

            // Construct hint tab
            $tab = array(
                'id'      => 'redux-hint-tab',
                'title'   => __( 'Hints', 'mozart-options-demo' ),
                'content' => __( '<p>' . $msg . '</p>', 'mozart-options-demo' )
            );

            $screen->add_help_tab( $tab );
        }

        // Sidebar text
        if ($this->params['help_sidebar'] != '') {

            // Specify users text from arguments
            $screen->set_help_sidebar( $this->params['help_sidebar'] );
        } else {

            // If sidebar text is empty and hints are active, display text
            // about hints.
            if (true == $this->show_hints) {
                $screen->set_help_sidebar(
                    '<p><strong>Options</strong><br/><br/>Hint Tooltip Preferences</p>'
                );
            }
        }
    }

    /**
     * Return footer text
     * @return string $this->params['footer_credit']
     */
    public function admin_footer_text()
    {
        return $this->params['footer_credit'];
    }

    /**
     * Return default output string for use in panel
     *
     * @param $field
     * @return string default_output
     */
    public function get_default_output_string( $field )
    {
        $default_output = "";

        if (!isset( $field['default'] )) {
            $field['default'] = "";
        }

        if (!is_array( $field['default'] )) {
            if (!empty( $field['options'][$field['default']] )) {
                if (!empty( $field['options'][$field['default']]['alt'] )) {
                    $default_output .= $field['options'][$field['default']]['alt'] . ', ';
                } else {
                    // TODO: This serialize fix may not be the best solution. Look into it. PHP 5.4 error without serialize
                    if (!is_array( $field['options'][$field['default']] )) {
                        $default_output .= $field['options'][$field['default']] . ", ";
                    } else {
                        $default_output .= serialize( $field['options'][$field['default']] ) . ", ";
                    }
                }
            } elseif (!empty( $field['options'][$field['default']] )) {
                $default_output .= $field['options'][$field['default']] . ", ";
            } elseif (!empty( $field['default'] )) {
                if ($field['type'] == 'switch') {
                    $default_output .= ( $field['default'] == 1 ? $field['on'] : $field['off'] ) . ', ';
                } else {
                    $default_output .= $field['default'] . ', ';
                }
            }
        } else {
            foreach ($field['default'] as $defaultk => $defaultv) {
                if (!empty( $field['options'][$defaultv]['alt'] )) {
                    $default_output .= $field['options'][$defaultv]['alt'] . ', ';
                } elseif (!empty( $field['options'][$defaultv] )) {
                    $default_output .= $field['options'][$defaultv] . ", ";
                } elseif (!empty( $field['options'][$defaultk] )) {
                    $default_output .= $field['options'][$defaultk] . ", ";
                } elseif (!empty( $defaultv )) {
                    $default_output .= $defaultv . ', ';
                }
            }
        }

        if (!empty( $default_output )) {
            $default_output = __( 'Default', 'mozart-options' ) . ": " . substr( $default_output, 0, -2 );
        }

        if (!empty( $default_output )) {
            $default_output = '<span class="showDefaults">' . $default_output . '</span><br class="default_br" />';
        }

        return $default_output;
    }

    /**
     * @param $field
     * @return string
     */
    public function get_header_html( $field )
    {
        global $current_user;

        // Set to empty string to avoid wanrings.
        $hint = '';
        $th = "";

        if (isset( $field['title'] ) && isset( $field['type'] ) && $field['type'] !== "info" && $field['type'] !== "section") {
            $default_mark = ( !empty( $field['default'] ) && isset( $this->options[$field['id']] ) && $this->options[$field['id']] == $field['default'] && !empty( $this->params['default_mark'] ) && isset( $field['default'] ) ) ? $this->params['default_mark'] : '';

            // If a hint is specified in the field, process it.
            if (isset( $field['hint'] ) && !'' == $field['hint']) {

                // Set show_hints flag to true, so helptab will be displayed.
                $this->show_hints = true;

                // Get user pref for displaying hints.
                $metaVal = get_user_meta( $current_user->ID, 'ignore_hints', true );
                if ('true' == $metaVal || empty( $metaVal )) {

                    // Set hand cursor for clickable hints
                    $pointer = '';
                    if (isset( $this->params['hints']['tip_effect']['show']['event'] ) && 'click' == $this->params['hints']['tip_effect']['show']['event']) {
                        $pointer = 'pointer';
                    }

                    $size = '16px';
                    if ('large' == $this->params['hints']['icon_size']) {
                        $size = '18px';
                    }

                    // In case docs are ignored.
                    $titleParam = isset( $field['hint']['title'] ) ? $field['hint']['title'] : '';
                    $contentParam = isset( $field['hint']['content'] ) ? $field['hint']['content'] : '';

                    $hint_color = isset( $this->params['hints']['icon_color'] ) ? $this->params['hints']['icon_color'] : '#d3d3d3';

                    // Set hint html with appropriate position css
                    $hint = '<div class="redux-hint-qtip" style="float:' . $this->params['hints']['icon_position'] . '; font-size: ' . $size . '; color:' . $hint_color . '; cursor: ' . $pointer . ';" qtip-title="' . $titleParam . '" qtip-content="' . $contentParam . '"><i class="el-icon-question-sign"></i>&nbsp&nbsp</div>';
                }
            }

            if (!empty( $field['title'] )) {
                if ('left' == $this->params['hints']['icon_position']) {
                    $th = $hint . $field['title'] . $default_mark . "";
                } else {
                    $th = $field['title'] . $default_mark . "" . $hint;
                }
            }

            if (isset( $field['subtitle'] )) {
                $th .= '<span class="description">' . $field['subtitle'] . '</span>';
            }
        }

        if (!empty( $th )) {
            $th = '<div class="redux_field_th">' . $th . '</div>';
        }

        if ($this->params['default_show'] === true && isset( $field['default'] ) && isset( $this->options[$field['id']] ) && $this->options[$field['id']] != $field['default'] && $field['type'] !== "info" && $field['type'] !== "group" && $field['type'] !== "section" && $field['type'] !== "editor" && $field['type'] !== "ace_editor") {
            $th .= $this->get_default_output_string( $field );
        }

        return $th;
    }


    /**
     * Register Option for use
     *
     * @return void
     */
    public function registerSettings()
    {
        if (!function_exists( 'wp_get_current_user' )) {
            include( ABSPATH . "wp-includes/pluggable.php" );
        }

        register_setting(
            $this->params['opt_name'] . '_group',
            $this->params['opt_name'],
            array(
                $this,
                '_validate_options'
            )
        );

        $sections = $this->getSectionManager()->getSections();
        if (empty( $sections )) {
            return;
        }

        $this->options_defaults = $this->getDefaultOptions();

        $runUpdate = false;

        foreach ($sections as $k => $section) {
            if (isset( $section['type'] ) && $section['type'] == 'divide') {
                continue;
            }

            $display = true;

            if (isset( $_GET['page'] ) && $_GET['page'] == $this->params['page_slug']) {
                if (isset( $section['panel'] ) && $section['panel'] == false) {
                    $display = false;
                }
            }

            if (!$display) {
                continue;
            }

            if (!isset( $section['title'] )) {
                $section['title'] = "";
            }

            $heading = isset( $section['heading'] ) ? $section['heading'] : $section['title'];

            if (isset( $section['permissions'] )) {
                if (!current_user_can( $section['permissions'] )) {
                    $this->hidden_perm_sections[] = $section['title'];

                    foreach ($section['fields'] as $num => $field_data) {
                        $field_type = $field_data['type'];

                        if ($field_type != 'section' || $field_type != 'divide' || $field_type != 'info' || $field_type != 'raw') {
                            $field_id = $field_data['id'];
                            $default = isset( $this->options_defaults[$field_id] ) ? $this->options_defaults[$field_id] : '';
                            $data = isset( $this->options[$field_id] ) ? $this->options[$field_id] : $default;

                            $this->getFieldManager()->addHiddenField($field_id, $data);
                        }
                    }

                    continue;
                }
            }

            add_settings_section(
                $this->params['opt_name'] . $k . '_section',
                $heading,
                array(
                    &$this,
                    '_section_desc'
                ),
                $this->params['opt_name'] . $k . '_section_group'
            );

            $sectionIndent = false;
            if (isset( $section['fields'] )) {
                foreach ($section['fields'] as $fieldk => $field) {
                    if (!isset( $field['type'] )) {
                        continue; // You need a type!
                    }

                    if (isset( $field['customizer_only'] ) && $field['customizer_only'] == true) {
                        continue; // ok
                    }

                    $display = true;
                    if (isset( $_GET['page'] ) && $_GET['page'] == $this->params['page_slug']) {
                        if (isset( $field['panel'] ) && $field['panel'] == false) {
                            $display = false;
                        }
                    }

                    if (!$display) {
                        continue;
                    }


                    if (isset( $field['permissions'] )) {

                        if (!current_user_can( $field['permissions'] )) {
                            $data = isset( $this->options[$field['id']] ) ? $this->options[$field['id']] : $this->options_defaults[$field['id']];

                            $this->getFieldManager()->addHiddenField($field['id'], $data);
                            continue;
                        }
                    }

                    if (!isset( $field['id'] )) {
                        echo '<br /><h3>No field ID is set.</h3><pre>';
                        print_r( $field );
                        echo "</pre><br />";
                        continue;
                    }

                    if (isset( $field['type'] ) && $field['type'] == "section") {
                        if (isset( $field['indent'] ) && $field['indent'] == true) {
                            $sectionIndent = true;
                        } else {
                            $sectionIndent = false;
                        }
                    }

                    if (isset( $field['type'] ) && $field['type'] == "info" && $sectionIndent) {
                        $field['indent'] = $sectionIndent;
                    }

                    $th = $this->get_header_html( $field );

                    $field['name'] = $this->params['opt_name'] . '[' . $field['id'] . ']';

                    // Set the default value if present
                    $this->options_defaults[$field['id']] = isset( $this->options_defaults[$field['id']] ) ? $this->options_defaults[$field['id']] : '';

                    // Set the defaults to the value if not present
                    $doUpdate = false;

                    // Check fields for values in the default parameter
                    if (!isset( $this->options[$field['id']] ) && isset( $field['default'] )) {
                        $this->options_defaults[$field['id']] = $this->options[$field['id']] = $field['default'];
                        $doUpdate = true;

                        // Check fields that hae no default value, but an options value with settings to
                        // be saved by default
                    } elseif (!isset( $this->options[$field['id']] ) && isset( $field['options'] )) {

                        // If sorter field, check for options as save them as defaults
                        if ($field['type'] == 'sorter' || $field['type'] == 'sortable') {
                            $this->options_defaults[$field['id']] = $this->options[$field['id']] = $field['options'];
                            $doUpdate = true;
                        }
                    }

                    // CORRECT URLS if media URLs are wrong, but attachment IDs are present.
                    if ($field['type'] == "media") {
                        if (isset( $this->options[$field['id']]['id'] ) && isset( $this->options[$field['id']]['url'] ) && !empty( $this->options[$field['id']]['url'] ) && strpos(
                                $this->options[$field['id']]['url'],
                                str_replace( 'http://', '', WP_CONTENT_URL )
                            ) === false
                        ) {
                            $data = wp_get_attachment_url( $this->options[$field['id']]['id'] );

                            if (isset( $data ) && !empty( $data )) {
                                $this->options[$field['id']]['url'] = $data;
                                $data = wp_get_attachment_image_src(
                                    $this->options[$field['id']]['id'],
                                    array(
                                        150,
                                        150
                                    )
                                );
                                $this->options[$field['id']]['thumbnail'] = $data[0];
                                $doUpdate = true;
                            }
                        }
                    }

                    if ($field['type'] == "background") {
                        if (isset( $this->options[$field['id']]['media']['id'] ) && isset( $this->options[$field['id']]['background-image'] ) && !empty( $this->options[$field['id']]['background-image'] ) && strpos(
                                $this->options[$field['id']]['background-image'],
                                str_replace( 'http://', '', WP_CONTENT_URL )
                            ) === false
                        ) {
                            $data = wp_get_attachment_url( $this->options[$field['id']]['media']['id'] );

                            if (isset( $data ) && !empty( $data )) {
                                $this->options[$field['id']]['background-image'] = $data;
                                $data = wp_get_attachment_image_src(
                                    $this->options[$field['id']]['media']['id'],
                                    array(
                                        150,
                                        150
                                    )
                                );
                                $this->options[$field['id']]['media']['thumbnail'] = $data[0];
                                $doUpdate = true;
                            }
                        }
                    }

                    if ($field['type'] == "slides") {
                        if (isset( $this->options[$field['id']][0]['attachment_id'] ) && isset( $this->options[$field['id']][0]['image'] ) && !empty( $this->options[$field['id']][0]['image'] ) && strpos(
                                $this->options[$field['id']][0]['image'],
                                str_replace( 'http://', '', WP_CONTENT_URL )
                            ) === false
                        ) {
                            foreach ($this->options[$field['id']] as $k => $v) {
                                $data = wp_get_attachment_url( $v['attachment_id'] );

                                if (isset( $data ) && !empty( $data )) {
                                    $this->options[$field['id']][$k]['image'] = $data;
                                    $data = wp_get_attachment_image_src(
                                        $v['attachment_id'],
                                        array(
                                            150,
                                            150
                                        )
                                    );
                                    $this->options[$field['id']][$k]['thumb'] = $data[0];
                                    $doUpdate = true;
                                }
                            }
                        }
                    }
                    if (true == $doUpdate && !isset( $this->never_save_to_db )) {
                        if ($this->params['save_defaults']) { // Only save that to the DB if allowed to
                            $runUpdate = true;
                        }
                    }

                    $folds = $this->getFieldManager()->getFolds();

                    $field['class'] = isset($field['class']) ? $field['class'] : "";

                    if (!empty( $folds[$field['id']]['parent'] )) { // This has some fold items, hide it by default
                        $field['class'] .= " fold";
                    }

                    if (!empty( $folds[$field['id']]['children'] )) { // Sets the values you shoe fold children on
                        $field['class'] .= " foldParent";
                    }

                    if (!empty( $field['compiler'] )) {
                        $field['class'] .= " compiler";
                        $this->getFieldManager()->addCompilerField($field['id']);
                    }

                    $this->getSectionManager()->updateSection($k, array(
                            'fields' => array(
                                $fieldk => $field
                            )
                        )
                    );

//                    if (isset( $this->params['display_source'] )) {
//                        $th .= '<div id="' . $field['id'] . '-settings" style="display:none;"><pre>' . var_export(
//                                $this->sections[$k]['fields'][$fieldk],
//                                true
//                            ) . '</pre></div>';
//                        $th .= '<br /><a href="#TB_inline?width=600&height=800&inlineId=' . $field['id'] . '-settings" class="thickbox"><small>View Source</small></a>';
//                    }

                    $this->getFieldManager()->checkDependencies( $field );

                    add_settings_field(
                        "{$fieldk}_field",
                        $th,
                        array( $this->getFieldManager(), 'fieldInput' ),
                        "{$this->params['opt_name']}{$k}_section_group",
                        "{$this->params['opt_name']}{$k}_section",
                        $field
                    );
                }
            }
        }

        if ($runUpdate && !isset( $this->never_save_to_db )) { // Always update the DB with new fields
            $this->setOptions( $this->options );
        }

        if (isset( $this->transients['run_compiler'] ) && $this->transients['run_compiler']) {
            $this->params['output_tag'] = false;
            $this->enqueueScriptsOutput();

            unset( $this->transients['run_compiler'] );
            $this->setTransients();
        }
    }

    /**
     *
     */
    public function getTransients()
    {
        if (!isset( $this->transients )) {
            $this->transients = get_option( $this->params['opt_name'] . '-transients', array() );
            $this->transients_check = $this->transients;
        }
    }

    /**
     *
     */
    public function setTransients()
    {
        if (!isset( $this->transients ) || !isset( $this->transients_check ) || $this->transients != $this->transients_check) {
            update_option( $this->params['opt_name'] . '-transients', $this->transients );
            $this->transients_check = $this->transients;
        }
    }

    /**
     * Validate the Options options before insertion
     *
     * @param array $plugin_options The options array
     *
     * @return array|mixed|string|void
     */
    public function _validate_options( $plugin_options )
    {
        if (!empty( $this->hidden_perm_fields ) && is_array( $this->hidden_perm_fields )) {
            foreach ($this->hidden_perm_fields as $id => $data) {
                $plugin_options[$id] = $data;
            }
        }

        if ($plugin_options == $this->options) {
            return $plugin_options;
        }

        $time = time();

        // Sets last saved time
        $this->transients['last_save'] = $time;

        // Import
        if (!empty( $plugin_options['import'] )) {
            $this->transients['last_save_mode'] = "import"; // Last save mode
            $this->transients['last_compiler'] = $time;
            $this->transients['last_import'] = $time;
            $this->transients['run_compiler'] = 1;

            if ($plugin_options['import_code'] != '') {
                $import = $plugin_options['import_code'];
            } elseif ($plugin_options['import_link'] != '') {
                $import = wp_remote_retrieve_body( wp_remote_get( $plugin_options['import_link'] ) );
            }

            if (!empty( $import )) {
                $imported_options = json_decode( $import, true );
            }

            if (!empty( $imported_options ) && is_array(
                    $imported_options
                ) && isset( $imported_options['redux-backup'] ) && $imported_options['redux-backup'] == '1'
            ) {

                $this->transients['changed_values'] = array();
                foreach ($plugin_options as $key => $value) {
                    if (isset( $imported_options[$key] ) && $imported_options[$key] != $value) {
                        $this->transients['changed_values'][$key] = $value;
                        $plugin_options[$key] = $value;
                    }
                }

                // Remove the import/export tab cookie.
                if ($_COOKIE['redux_current_tab'] == 'importer_default') {
                    setcookie( 'redux_current_tab', '', 1, '/' );
                    $_COOKIE['redux_current_tab'] = 1;
                }

                setcookie( 'redux_current_tab', '', 1, '/', $time + 1000, "/" );
                $_COOKIE['redux_current_tab'] = 1;

                unset( $plugin_options['defaults'], $plugin_options['compiler'], $plugin_options['import'], $plugin_options['import_code'] );
                if ($this->params['database'] == 'transient' || $this->params['database'] == 'theme_mods' || $this->params['database'] == 'theme_mods_expanded' || $this->params['database'] == 'network') {
                    $this->setOptions( $plugin_options );

                    return false;
                }

                $plugin_options = array_merge( $plugin_options, $imported_options );
                $this->setTransients();

                return $plugin_options;
            }
        }

        // Reset all to defaults
        if (!empty( $plugin_options['defaults'] )) {
            if (empty( $this->options_defaults )) {
                $this->options_defaults = $this->getDefaultOptions();
            }

            $plugin_options = $this->options_defaults;

            $this->transients['changed_values'] = array();
            foreach ($this->options as $key => $value) {
                if (isset( $plugin_options[$key] ) && $value != $plugin_options[$key]) {
                    $this->transients['changed_values'][$key] = $value;
                }
            }

            $this->transients['run_compiler'] = 1;
            $this->transients['last_save_mode'] = "defaults"; // Last save mode

            $this->setTransients(); // Update the transients

            return $plugin_options;
        }

        // Section reset to defaults
        if (!empty( $plugin_options['defaults-section'] )) {
            if (isset( $plugin_options['redux-section'] )) {
                foreach ((array)$this->getSectionManager()->getSection($plugin_options['redux-section'])['fields'] as $field) {
                    if (isset( $this->options_defaults[$field['id']] )) {
                        $plugin_options[$field['id']] = $this->options_defaults[$field['id']];
                    } else {
                        $plugin_options[$field['id']] = "";
                    }

                    if (isset( $field['compiler'] )) {
                        $compiler = true;
                    }
                }
            }

            $this->transients['changed_values'] = array();
            foreach ($this->options as $key => $value) {
                if (isset( $plugin_options[$key] ) && $value != $plugin_options[$key]) {
                    $this->transients['changed_values'][$key] = $value;
                }
            }

            if (isset( $compiler )) {
                $this->transients['last_compiler'] = $time;
                $this->transients['run_compiler'] = 1;
            }

            $this->transients['last_save_mode'] = "defaults_section"; // Last save mode

            unset( $plugin_options['defaults'], $plugin_options['defaults_section'], $plugin_options['import'], $plugin_options['import_code'], $plugin_options['import_link'], $plugin_options['compiler'], $plugin_options['redux-section'] );
            $this->setTransients();

            return $plugin_options;
        }

        $this->transients['last_save_mode'] = "normal"; // Last save mode

        // Validate fields (if needed)
        $plugin_options = $this->getValidator()->_validate_values(
            $plugin_options,
            $this->options,
            $this->getSectionManager()->getSections()
        );

        if (count( $this->getValidator()->getErrors() ) > 0 ||
            count( $this->getValidator()->getWarnings() ) > 0) {
            $this->transients['notices'] = array(
                'errors' => $this->getValidator()->getErrors(),
                'warnings' => $this->getValidator()->getWarnings()
            );
        }


        if (!empty( $plugin_options['compiler'] )) {
            unset( $plugin_options['compiler'] );

            $this->transients['last_compiler'] = $time;
            $this->transients['run_compiler'] = 1;
        }

        $this->transients['changed_values'] = array(); // Changed values since last save
        foreach ($this->options as $key => $value) {
            if (isset( $plugin_options[$key] ) && $value != $plugin_options[$key]) {
                $this->transients['changed_values'][$key] = $value;
            }
        }

        unset( $plugin_options['defaults'], $plugin_options['defaults_section'], $plugin_options['import'], $plugin_options['import_code'], $plugin_options['import_link'], $plugin_options['compiler'], $plugin_options['redux-section'] );
        if ($this->params['database'] == 'transient' || $this->params['database'] == 'theme_mods' || $this->params['database'] == 'theme_mods_expanded') {
            $this->setOptions( $plugin_options );

            return false;
        }

//        if (defined( 'WP_CACHE' ) && WP_CACHE && class_exists( '\W3_ObjectCache' )) {
//            $w3 = W3_ObjectCache::instance();
//            $key = $w3->_get_cache_key( $this->params['opt_name'] . '-transients', 'transient' );
//            $w3->delete( $key, 'transient', true );
//        }

        $this->setTransients( $this->transients );

        return $plugin_options;
    }


    /**
     * Return Section Menu HTML
     * @param $k
     * @param $section
     * @param string $suffix
     * @param array $sections
     * @return string
     */
    public function section_menu( $k, $section, $suffix = "", $sections = array() )
    {
        $string = "";
        $display = true;

        $section['class'] = isset( $section['class'] ) ? ' ' . $section['class'] : '';

        if (isset( $_GET['page'] ) && $_GET['page'] == $this->params['page_slug']) {
            if (isset( $section['panel'] ) && $section['panel'] == false) {
                $display = false;
            }
        }

        if (!$display) {
            return $string;
        }

        if (empty( $sections )) {
            $sections = $this->getSectionManager()->getSections();
        }

        if (( isset( $this->params['icon_type'] ) && $this->params['icon_type'] == 'image' ) || ( isset( $section['icon_type'] ) && $section['icon_type'] == 'image' )) {
            $icon = ( !isset( $section['icon'] ) ) ? '' : '<img class="image_icon_type" src="' . $section['icon'] . '" /> ';
        } else {
            if (!empty( $section['icon_class'] )) {
                $icon_class = ' ' . $section['icon_class'];
            } elseif (!empty( $this->params['default_icon_class'] )) {
                $icon_class = ' ' . $this->params['default_icon_class'];
            } else {
                $icon_class = '';
            }
            $icon = ( !isset( $section['icon'] ) ) ? '<i class="el-icon-cog' . $icon_class . '"></i> ' : '<i class="' . $section['icon'] . $icon_class . '"></i> ';
        }

        $canBeSubSection = ( $k > 0 && ( !isset( $sections[( $k )]['type'] ) || $sections[( $k )]['type'] != "divide" ) ) ? true : false;

        if (!$canBeSubSection && isset( $section['subsection'] ) && $section['subsection'] == true) {
            unset( $section['subsection'] );
        }

        if (isset( $section['type'] ) && $section['type'] == "divide") {
            $string .= '<li class="divide' . $section['class'] . '">&nbsp;</li>';
        } elseif (!isset( $section['subsection'] ) || $section['subsection'] != true) {

            $subsections = ( isset( $sections[( $k + 1 )] ) && isset( $sections[( $k + 1 )]['subsection'] ) && $sections[( $k + 1 )]['subsection'] == true ) ? true : false;
            $subsectionsClass = $subsections ? ' hasSubSections' : '';
            $extra_icon = $subsections ? '<span class="extraIconSubsections"><i class="el el-icon-chevron-down">&nbsp;</i></span>' : '';
            $string .= '<li id="' . $k . $suffix . '_section_group_li" class="redux-group-tab-link-li' . $section['class'] . $subsectionsClass . '">';
            $string .= '<a href="javascript:void(0);" id="' . $k . $suffix . '_section_group_li_a" class="redux-group-tab-link-a" data-key="' . $k . '" data-rel="' . $k . $suffix . '">' . $extra_icon . $icon . '<span class="group_title">' . $section['title'] . '</span></a>';
            $nextK = $k;

            // Make sure you can make this a subsection
            if ($subsections) {
                $string .= '<ul id="' . $nextK . $suffix . '_section_group_li_subsections" class="subsection">';
                $doLoop = true;

                while ($doLoop) {
                    $nextK += 1;
                    $display = true;

                    if (isset( $_GET['page'] ) && $_GET['page'] == $this->params['page_slug']) {
                        if (isset( $sections[$nextK]['panel'] ) && $sections[$nextK]['panel'] == false) {
                            $display = false;
                        }
                    }

                    if (count(
                            $sections
                        ) < $nextK || !isset( $sections[$nextK] ) || !isset( $sections[$nextK]['subsection'] ) || $sections[$nextK]['subsection'] != true
                    ) {
                        $doLoop = false;
                    } else {
                        if (!$display) {
                            continue;
                        }

                        if (( isset( $this->params['icon_type'] ) && $this->params['icon_type'] == 'image' ) || ( isset( $sections[$nextK]['icon_type'] ) && $sections[$nextK]['icon_type'] == 'image' )) {
                            $icon = ( !isset( $sections[$nextK]['icon'] ) ) ? '' : '<img class="image_icon_type" src="' . $sections[$nextK]['icon'] . '" /> ';
                        } else {
                            if (!empty( $sections[$nextK]['icon_class'] )) {
                                $icon_class = ' ' . $sections[$nextK]['icon_class'];
                            } elseif (!empty( $this->params['default_icon_class'] )) {
                                $icon_class = ' ' . $this->params['default_icon_class'];
                            } else {
                                $icon_class = '';
                            }
                            $icon = ( !isset( $sections[$nextK]['icon'] ) ) ? '' : '<i class="' . $sections[$nextK]['icon'] . $icon_class . '"></i> ';
                        }
                        $section[$nextK]['class'] = isset( $section[$nextK]['class'] ) ? $section[$nextK]['class'] : '';
                        $string .= '<li id="' . $nextK . $suffix . '_section_group_li" class="redux-group-tab-link-li ' . $section[$nextK]['class'] . ( $icon ? ' hasIcon' : '' ) . '">';
                        $string .= '<a href="javascript:void(0);" id="' . $nextK . $suffix . '_section_group_li_a" class="redux-group-tab-link-a" data-key="' . $nextK . '" data-rel="' . $nextK . $suffix . '">' . $icon . '<span class="group_title">' . $sections[$nextK]['title'] . '</span></a>';
                        $string .= '</li>';
                    }
                }

                $string .= '</ul>';
            }

            $string .= '</li>';
        }

        return $string;

    }

    /**
     * HTML OUTPUT.
     *
     * @return void
     */
    public function _options_page_html()
    {
        $url = './options.php';
        if ($this->params['database'] == "network" && $this->params['network_admin']) {
            if (is_network_admin()) {
                $url = './edit.php?action=redux_' . $this->params['opt_name'];
            }
        }

        $message = '';

        // Warning bar
        if (isset( $this->transients['last_save_mode'] )) {

            if ($this->transients['last_save_mode'] == "import") {
                $message = '<div class="admin-notice notice-blue saved_notice"><strong>'
                    . __( 'Settings Imported!', 'mozart-options' )
                    . '</strong></div>';
            } elseif ($this->transients['last_save_mode'] == "defaults") {
                $message = '<div class="saved_notice admin-notice notice-yellow"><strong>' .
                    __( 'All Defaults Restored!', 'mozart-options' )
                    . '</strong></div>';
            } elseif ($this->transients['last_save_mode'] == "defaults_section") {

                $message = '<div class="saved_notice admin-notice notice-yellow"><strong>' .
                    __( 'Section Defaults Restored!', 'mozart-options' )
                    . '</strong></div>';
            } else {
                $message = '<div class="saved_notice admin-notice notice-green"><strong>' .
                    __( 'Settings Saved!', 'mozart-options' )
                    . '</strong></div>';
            }
            unset( $this->transients['last_save_mode'] );
        }

        $sectionsOutput = '';
        $mainContent = '';
        foreach ($this->getSectionManager()->getSections() as $k => $section) {
            $title = isset( $section['title'] ) ? $section['title'] : '';

            $skip_sec = false;
            foreach ($this->hidden_perm_sections as $num => $section_title) {
                if ($section_title == $title) {
                    $skip_sec = true;
                }
            }

            if (isset( $section['customizer_only'] ) && $section['customizer_only'] == true) {
                continue;
            }

            if (false === $skip_sec) {
                $sectionsOutput .= $this->section_menu( $k, $section );
            }

            $section['class'] = isset( $section['class'] ) ? ' ' . $section['class'] : '';
            $mainContent .= '<div id="' . $k . '_section_group' . '" class="redux-group-tab' . $section['class'] . '" data-rel="' . $k . '">';

            $display = true;
            if (isset( $_GET['page'] ) && $_GET['page'] == $this->params['page_slug']) {
                if (isset( $section['panel'] ) && $section['panel'] == "false") {
                    $display = false;
                }
            }

            if ($display) {
                $mainContent .= $this->doSettingsSections( $this->params['opt_name'] . $k . '_section_group' );
            }
            $mainContent .= "</div>";
        }

        // Import / Export output
        if (true == $this->params['show_importer'] && false == $this->container->get(
                'mozart.option.importer'
            )->isEnabled()
        ) {
            $mainContent .= '<fieldset id="' . $this->params['opt_name'] . '-importer_core" class="redux-field-container redux-field redux-field-init redux-container-importer" data-id="importer_core" data-type="importer">';
            $mainContent .= $this->getImporter()->render();
            $mainContent .= '</fieldset>';
        }

        // Debug object output
        if ($this->params['dev_mode'] == true) {
            $mainContent .= $this->getDebugger()->render();
        }


        $context = array(
            'nonce'          => wp_create_nonce( 'redux_ajax_nonce' ),
            'page_nonce'     => wp_create_nonce( "{$this->params['opt_name']}_group-options" ),
            'params'         => $this->params,
            'url'            => $url,
            'referer'        => wp_unslash( $_SERVER['REQUEST_URI'] ),
            'last_tab'       => ( isset( $_GET['tab'] ) && !isset( $this->transients['last_save_mode'] ) ) ? $_GET['tab'] : '',
            'message'        => $message,
            'sectionsOutput' => $sectionsOutput,
            'mainContent'    => $mainContent,
            'systemInfo'     => ''
//            'systemInfo' => SystemInfo::get()
        );

        echo $this->container->get( 'templating' )->render( 'MozartOptionBundle:Option:page.html.twig', $context );

        $this->setTransients();

    }

    /**
     * @param $page
     * @return string
     */
    private function doSettingsSections( $page )
    {
        global $wp_settings_sections, $wp_settings_fields;

        $return = '';

        if (!isset( $wp_settings_sections[$page] )) {
            return $return;
        }

        foreach ((array)$wp_settings_sections[$page] as $section) {
            if ($section['title']) {
                $return .= "<h3>{$section['title']}</h3>\n";
            }

            if ($section['callback']) {
                call_user_func( $section['callback'], $section );
            }

            if (!isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] )) {
                continue;
            }
            $return .= '<table class="form-table">';
            $return .= $this->doSettingsFields( $page, $section['id'] );
            $return .= '</table>';
        }

        return $return;
    }

    /**
     * @param $page
     * @param $section
     * @return string
     */
    private function doSettingsFields( $page, $section )
    {
        global $wp_settings_fields;
        $return = '';

        if (!isset( $wp_settings_fields[$page][$section] )) {
            return $return;
        }

        foreach ((array)$wp_settings_fields[$page][$section] as $field) {
            $return .= '<tr>';
            if (!empty( $field['args']['label_for'] )) {
                $return .= '<th scope="row"><label for="' . esc_attr(
                        $field['args']['label_for']
                    ) . '">' . $field['title'] . '</label></th>';
            } else {
                $return .= '<th scope="row">' . $field['title'] . '</th>';
            }
            $return .= '<td>';
            $return .= call_user_func( $field['callback'], $field['args'] );
            $return .= '</td>';
            $return .= '</tr>';
        }
        return $return;
    }

    /**
     * Section HTML OUTPUT.
     *
     * @param array $section
     *
     * @return void
     */
    public function _section_desc( $section )
    {
        $id = trim( rtrim( $section['id'], '_section' ), $this->params['opt_name'] );

        if ($this->getSectionManager()->getSection($id)['desc'] != '' ) {
            echo '<div class="redux-section-desc">' . $this->getSectionManager()->getSection($id)['desc'] . '</div>';
        }
    }

    /**
     * Compare data for required field
     *
     * @param $parentValue
     * @param $checkValue
     * @param $operation
     *
     * @return bool
     */
    public function compareValueDependencies( $parentValue, $checkValue, $operation )
    {
        $return = false;

        switch ($operation) {
            case '=':
            case 'equals':
                $data['operation'] = "=";
                if (is_array( $checkValue )) {
                    if (in_array( $parentValue, $checkValue )) {
                        $return = true;
                    }
                } else {
                    if ($parentValue == $checkValue) {
                        $return = true;
                    } elseif (is_array( $parentValue )) {
                        if (in_array( $checkValue, $parentValue )) {
                            $return = true;
                        }
                    }
                }
                break;
            case '!=':
            case 'not':
                $data['operation'] = "!==";
                if (is_array( $checkValue )) {
                    if (!in_array( $parentValue, $checkValue )) {
                        $return = true;
                    }
                } else {
                    if ($parentValue != $checkValue) {
                        $return = true;
                    } elseif (is_array( $parentValue )) {
                        if (!in_array( $checkValue, $parentValue )) {
                            $return = true;
                        }
                    }
                }
                break;
            case '>':
            case 'greater':
            case 'is_larger':
                $data['operation'] = ">";
                if ($parentValue > $checkValue) {
                    $return = true;
                }
                break;
            case '>=':
            case 'greater_equal':
            case 'is_larger_equal':
                $data['operation'] = ">=";
                if ($parentValue >= $checkValue) {
                    $return = true;
                }
                break;
            case '<':
            case 'less':
            case 'is_smaller':
                $data['operation'] = "<";
                if ($parentValue < $checkValue) {
                    $return = true;
                }
                break;
            case '<=':
            case 'less_equal':
            case 'is_smaller_equal':
                $data['operation'] = "<=";
                if ($parentValue <= $checkValue) {
                    $return = true;
                }
                break;
            case 'contains':
                if (strpos( $parentValue, $checkValue ) !== false) {
                    $return = true;
                }
                break;
            case 'doesnt_contain':
            case 'not_contain':
                if (strpos( $parentValue, $checkValue ) === false) {
                    $return = true;
                }
                break;
            case 'is_empty_or':
                if (empty( $parentValue ) || $parentValue == $checkValue) {
                    $return = true;
                }
                break;
            case 'not_empty_and':
                if (!empty( $parentValue ) && $parentValue != $checkValue) {
                    $return = true;
                }
                break;
            case 'is_empty':
            case 'empty':
            case '!isset':
                if (empty( $parentValue ) || $parentValue == "" || $parentValue == null) {
                    $return = true;
                }
                break;
            case 'not_empty':
            case '!empty':
            case 'isset':
                if (!empty( $parentValue ) && $parentValue != "" && $parentValue != null) {
                    $return = true;
                }
                break;
        }

        return $return;
    }


    /**
     * converts an array into a html data string
     *
     * @param array $data example input: array('id'=>'true')
     *
     * @return string $data_string example output: data-id='true'
     */
    public function create_data_string( $data = array() )
    {
        $data_string = "";

        foreach ($data as $key => $value) {
            if (is_array( $value )) {
                $value = implode( "|", $value );
            }
            $data_string .= " data-$key='$value' ";
        }

        return $data_string;
    }


    /**
     * @param $fields
     * @param array $field
     * @return bool
     */
    public function isFieldInUseByType( $fields, $field = array() )
    {
        foreach ($field as $name) {
            if (array_key_exists( $name, $fields )) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $sections
     * @param $field
     * @return bool
     */
    public function isFieldInUse( $sections, $field )
    {
        foreach ($sections as $k => $section) {
            if (!isset( $section['title'] )) {
                continue;
            }

            if (isset( $section['fields'] ) && !empty( $section['fields'] )) {
                if (in_array_recursive( $field, $section['fields'] )) {
                    return true;
                }
            }
        }

        return false;
    }
}
