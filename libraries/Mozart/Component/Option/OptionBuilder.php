<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Option;

use Mozart\Component\Debug\SystemInfo;
use Mozart\Component\Form\Field\Typography;
use Mozart\Component\Option\Utils\OptionUtil;
use Mozart\Component\Support\Str;
use Mozart\Component\Option\Extension\ExtensionManager;
use Mozart\Component\Option\Section\SectionManager;

/**
 * Class OptionBuilder
 * @package Mozart\Component\Option
 */
class OptionBuilder implements OptionBuilderInterface
{
    /**
     *
     */
    const NAME = 'mozart';
    /**
     * @var
     */
    public static $_dir;
    /**
     * @var
     */
    public static $_url;
    /**
     * @var
     */
    public static $_properties;
    /**
     * @var bool
     */
    public static $_as_plugin = false;
    /**
     * @var null
     */
    public static $instance = null;

    /**
     * @var string
     */
    public $framework_url = 'http://www.reduxframework.com/';
    /**
     * @var array
     */
    public $wp_data = array();
    /**
     * @var array
     */
    public $font_groups = array();
    /**
     * @var string
     */
    public $page = '';
    /**
     * @var bool
     */
    public $saved = false;
    /**
     * Fields by type used in the panel
     *
     * @var array
     */
    private $fields = array();
    /**
     * @var string
     */
    public $current_tab = ''; // Current section to display, cookies
    /**
     * @var array
     */
    protected $sections = array();
    /**
     * @var array
     */
    public $errors = array(); // Errors
    /**
     * @var array
     */
    public $warnings = array(); // Warnings
    /**
     * @var array
     */
    public $options = array(); // Option values
    /**
     * @var null
     */
    public $options_defaults = null; // Option defaults
    /**
     * @var array
     */
    public $notices = array(); // Option defaults
    /**
     * @var array
     */
    public $compiler_fields = array(); // Fields that trigger the compiler hook
    /**
     * @var array
     */
    public $required = array(); // Information that needs to be localized
    /**
     * @var array
     */
    public $required_child = array(); // Information that needs to be localized
    /**
     * @var array
     */
    public $localize_data = array(); // Information that needs to be localized
    /**
     * @var array
     */
    public $fonts = array(); // Information that needs to be localized
    /**
     * @var array
     */
    public $folds = array(); // The itms that need to fold.
    /**
     * @var string
     */
    public $path = '';
    /**
     * @var array
     */
    public $changed_values = array(); // Values that have been changed on save. Orig values.
    /**
     * @var array
     */
    public $output = array(); // Fields with CSS output selectors
    /**
     * @var null
     */
    public $outputCSS = null; // CSS that get auto-appended to the header
    /**
     * @var null
     */
    public $compilerCSS = null; // CSS that get sent to the compiler hook
    /**
     * @var null
     */
    public $customizerCSS = null; // CSS that goes to the customizer
    /**
     * @var array
     */
    public $fieldsValues = array(); //all fields values in an id=>value array so we can check dependencies
    /**
     * @var array
     */
    public $fieldsHidden = array(); //all fields that didn't pass the dependency test and are hidden
    /**
     * @var array
     */
    public $toHide = array(); // Values to hide on page load
    /**
     * @var null
     */
    public $typography = null; //values to generate google font CSS

    /**
     * @var array
     */
    public $typography_preview = array();
    /**
     * @var array
     */
    public $params = array();

    /**
     * @var bool
     */
    private $show_hints = false;
    /**
     * @var array
     */
    private $transients = array();
    /**
     * @var array
     */
    private $hidden_perm_fields = array(); //  Hidden fields specified by 'permissions' arg.
    /**
     * @var array
     */
    private $hidden_perm_sections = array(); //  Hidden sections specified by 'permissions' arg.

    /**
     * @var Importer
     */
    protected $importer;
    /**
     * @var Debugger
     */
    protected $debugger;
    /**
     * @var Tracker
     */
    protected $tracker;

    /**
     * @var ExtensionManager
     */
    private $extensionManager;
    /**
     * @var SectionManager
     */
    private $sectionManager;

    /**
     * @param Importer $importer
     * @param Debugger $debugger
     * @param Tracker $tracker
     * @param ExtensionManager $extensionManager
     * @param SectionManager $sectionManager
     */
    public function __construct(
        Importer $importer,
        Debugger $debugger,
        Tracker $tracker,
        ExtensionManager $extensionManager,
        SectionManager $sectionManager
    ) {
        $this->importer = $importer;
        $this->debugger = $debugger;
        $this->tracker = $tracker;
        $this->extensionManager = $extensionManager;
        $this->sectionManager = $sectionManager;
    }

    /**
     * @param array $params
     */
    public function boot( $params = array() )
    {
        $this->params = array_merge( $this->getDefaultArgs(), $params );

        if (empty( $this->params['opt_name'] )) {
            return false;
        }

        $this->setSections( $this->sectionManager->getSections() );

        if ($this->params['global_variable'] == "" && $this->params['global_variable'] !== false) {
            $this->params['global_variable'] = str_replace( '-', '_', $this->params['opt_name'] );
        }

        $this->loadExtensions();

        $this->loadTranslations();

        // Grab database values
        $this->loadOptions();

        $this->tracker->load( $this );

        // Display admin notices in dev_mode
        if (true == $this->params['dev_mode']) {
            $this->debugger->init( $this );
        }

        $this->importer->init( $this );

    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $field
     */
    public function addField( $field )
    {
        // Detect what field types are being used
        if (!isset( $this->fields[$field['type']][$field['id']] )) {
            $this->fields[$field['type']][$field['id']] = 1;
        } else {
            $this->fields[$field['type']] = array( $field['id'] => 1 );
        }
    }

    /**
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param array $sections
     */
    public function setSections( $sections )
    {

        $this->sections = $sections;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getParam( $param )
    {
        return $this->params[$param];
    }

    protected function loadExtensions()
    {

        foreach ($this->extensionManager->getExtensions() as $extension) {
            $extension->extend( $this );
        }
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
    public function network_admin_bar( \WP_Admin_Bar $wp_admin_bar )
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
                \Mozart::parameter( 'wp.plugin.dir' ) . '/mozart/translations/option/' . strtolower(
                    $locale
                ) . '_' . strtoupper( $locale ) . '.mo'
            )) {
                $locale = strtolower( $locale ) . '_' . strtoupper( $locale );
            }
        }
        load_textdomain(
            'mozart-options',
            \Mozart::parameter( 'wp.plugin.dir' ) . '/mozart/translations/option/' . $locale . '.mo'
        );
    }

    /**
     * This is used to return the default value if default_show is set
     *
     * @param       string $opt_name The option name to return
     * @param       mixed $default (null)  The value to return if default not set
     *
     * @return      mixed $default
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
     * @param       string $opt_name The option name to return
     * @param       mixed $default (null) The value to return if option not set
     *
     * @return      mixed
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
     * @param       string $opt_name The name of the option being added
     * @param       mixed $value The value of the option being added
     *
     * @return      void
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
     * @return  bool
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
            $this->set_transients();
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
     *
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
                        foreach ($this->getFontIcons() as $k) {
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
     * @param       string $opt_name The name of the option being shown
     * @param       mixed $default The value to show if $opt_name isn't set
     *
     * @return      void
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
     * @return      array
     */
    private function getDefaultOptions()
    {
        if (!is_null( $this->getSections() ) && is_null( $this->options_defaults )) {

            // fill the cache
            foreach ($this->getSections() as $sk => $section) {
                if (!isset( $section['id'] )) {
                    if (!is_numeric( $sk ) || !isset( $section['title'] )) {
                        $section['id'] = $sk;
                    } else {
                        $section['id'] = sanitize_title( $section['title'], $sk );
                    }
                    $this->sections[$sk] = $section;
                }
                if (isset( $section['fields'] )) {
                    foreach ($section['fields'] as $k => $field) {
                        if (empty( $field['id'] ) && empty( $field['type'] )) {
                            continue;
                        }
                        if ($field['type'] == "section" && isset( $field['indent'] ) && $field['indent'] == "true") {
                            $field['class'] = isset( $field['class'] ) ? $field['class'] : '';
                            $field['class'] .= "redux-section-indent-start";
                            $this->sections[$sk]['fields'][$k] = $field;
                        }

                        $this->addField( $field );

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
            }
        }

        /**
         * filter 'redux/options/{opt_name}/defaults'
         *
         * @param array $defaults option default values
         */
        $this->transients['changed_values'] = isset( $this->transients['changed_values'] ) ? $this->transients['changed_values'] : array();
        $this->options_defaults = apply_filters(
            "redux/options/{$this->params['opt_name']}/defaults",
            $this->options_defaults,
            $this->transients['changed_values']
        );

        return $this->options_defaults;
    }


    /**
     * Get fold values into an array suitable for setting folds
     */
    public function _fold_values()
    {

        if (!is_null( $this->getSections() )) {

            foreach ($this->getSections() as $section) {
                if (isset( $section['fields'] )) {
                    foreach ($section['fields'] as $field) {
                        if (isset( $field['fields'] ) && is_array( $field['fields'] )) {
                            foreach ($field['fields'] as $subfield) {
                                if (isset( $subfield['required'] )) {
                                    $this->get_fold( $subfield );
                                }
                            }
                        }
                        if (isset( $field['required'] )) {
                            $this->get_fold( $field );
                        }
                    }
                }
            }
        }

        $parents = array();

        foreach ($this->folds as $k => $fold) { // ParentFolds WITHOUT parents
            if (empty( $fold['children'] ) || !empty( $fold['children']['parents'] )) {
                continue;
            }

            $fold['value'] = $this->options[$k];

            foreach ($fold['children'] as $key => $value) {
                if ($key == $fold['value']) {
                    unset( $fold['children'][$key] );
                }
            }

            if (empty( $fold['children'] )) {
                continue;
            }

            foreach ($fold['children'] as $key => $value) {
                foreach ($value as $k => $hidden) {
                    if (!in_array( $hidden, $this->toHide )) {
                        $this->toHide[] = $hidden;
                    }
                }
            }

            $parents[] = $fold;
        }

        return $this->folds;
    }

    /**
     * Get the fold values
     *
     * @param array $field
     *
     * @return array
     */
    public function get_fold( $field )
    {
        if (!is_array( $field['required'] )) {

            /*
                Example variable:
                    $var = array(
                    'fold' => 'id'
                    );
                */

            $this->folds[$field['required']]['children'][1][] = $field['id'];
            $this->folds[$field['id']]['parent'] = $field['required'];
        } else {
//                $parent = $foldk = $field['required'][0];
            $foldk = $field['required'][0];
//                $comparison = $field['required'][1];
            $value = $foldv = $field['required'][2];
            //foreach ($field['required'] as $foldk=>$foldv) {


            if (is_array( $value )) {
                /*
                    Example variable:
                        $var = array(
                        'fold' => array( 'id' , '=', array(1, 5) )
                        );
                    */

                foreach ($value as $foldvValue) {
                    //echo 'id: '.$field['id']." key: ".$foldk.' f-val-'.print_r($foldv)." foldvValue".$foldvValue;
                    $this->folds[$foldk]['children'][$foldvValue][] = $field['id'];
                    $this->folds[$field['id']]['parent'] = $foldk;
                }
            } else {

                if (count( $field['required'] ) === 1 && is_numeric( $foldk )) {
                    /*
                        Example variable:
                            $var = array(
                            'fold' => array( 'id' )
                            );
                        */
                    $this->folds[$field['id']]['parent'] = $foldk;
                    $this->folds[$foldk]['children'][1][] = $field['id'];
                } else {
                    /*
                        Example variable:
                            $var = array(
                            'fold' => array( 'id' => 1 )
                            );
                        */
                    if (empty( $foldv )) {
                        $foldv = 0;
                    }

                    $this->folds[$field['id']]['parent'] = $foldk;
                    $this->folds[$foldk]['children'][$foldv][] = $field['id'];
                }
            }
            //}
        }

        return $this->folds;
    }

    /**
     * Class Add Sub Menu Function, creates options submenu in Wordpress admin area.
     *
     * @return      void
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
        $this->importer->in_field();

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
                if (!isset( $section['type'] ) || $section['type'] != 'divide') {
                    foreach ($this->getSections() as $k => $section) {
                        $canBeSubSection = ( $k > 0 && ( !isset( $this->sections[( $k )]['type'] ) || $this->sections[( $k )]['type'] != "divide" ) ) ? true : false;

                        if (!isset( $section['title'] ) || ( $canBeSubSection && ( isset( $section['subsection'] ) && $section['subsection'] == true ) )) {
                            continue;
                        }

                        if (isset( $section['submenu'] ) && $section['submenu'] == false) {
                            continue;
                        }

                        if (isset( $section['customizer_only'] ) && $section['customizer_only'] == true) {
                            continue;
                        }

                        add_submenu_page(
                            $this->params['page_slug'],
                            $section['title'],
                            $section['title'],
                            $this->params['page_permissions'],
                            $this->params['page_slug'] . '&tab=' . $k,
                            //create_function( '$a', "return null;" )
                            '__return_null'
                        );
                    }

                    // Remove parent submenu item instead of adding null item.
                    remove_submenu_page( $this->params['page_slug'], $this->params['page_slug'] );
                }

                if (true == $this->params['show_importer'] && false == $this->importer->is_field) {
                    $this->importer->add_submenu();
                }

                if (true == $this->params['dev_mode']) {
                    $this->debugger->add_submenu();
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
     * @global      $menu , $submenu, $wp_admin_bar
     * @return      void
     */
    public function _admin_bar_menu()
    {
        global $menu, $submenu, $wp_admin_bar;

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
     * @return      void
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
     * @return      void
     */
    public function _enqueue_output()
    {
        if ($this->params['output'] == false && $this->params['compiler'] == false) {
            return;
        }

        foreach ($this->getSections() as $k => $section) {
            if (isset( $section['type'] ) && ( $section['type'] == 'divide' )) {
                continue;
            }

            if (isset( $section['fields'] )) {
                foreach ($section['fields'] as $fieldk => $field) {
                    if (isset( $field['type'] ) && $field['type'] != "callback") {
                        $field_class = "Mozart\\Component\\Form\\Field\\" . Str::camel( $field['type'] );
                        if (!isset( $field['compiler'] )) {
                            $field['compiler'] = "";
                        }

                        if (false === class_exists( $field_class )) {
                            if (false === class_exists( $field_class . 'Field' )) {
                                continue;
                            } else {
                                $field_class = $field_class . 'Field';
                            }
                        }

                        if (!empty( $this->options[$field['id']] ) && method_exists(
                                $field_class,
                                'output'
                            ) && $this->_can_output_css( $field )
                        ) {
                            $field = apply_filters( "redux/field/{$this->params['opt_name']}/output_css", $field );

                            if (!empty( $field['output'] ) && !is_array( $field['output'] )) {
                                $field['output'] = array( $field['output'] );
                            }

                            $value = isset( $this->options[$field['id']] ) ? $this->options[$field['id']] : '';
                            $enqueue = new $field_class( $field, $value, $this );

                            if (( ( isset( $field['output'] ) && !empty( $field['output'] ) ) || ( isset( $field['compiler'] ) && !empty( $field['compiler'] ) ) || $field['type'] == "typography" || $field['type'] == "icon_select" )) {
                                $enqueue->output();
                            }
                        }
                    }
                }
            }
        }

        // For use like in the customizer. Stops the output, but passes the CSS in the variable for the compiler
        if (isset( $this->no_output )) {
            return;
        }

        if (!empty( $this->typography ) && !empty( $this->typography ) && filter_var(
                $this->params['output'],
                FILTER_VALIDATE_BOOLEAN
            )
        ) {
            $version = !empty( $this->transients['last_save'] ) ? $this->transients['last_save'] : '';
            $typography = new Typography( null, null, $this );

            if ($this->params['async_typography'] && !empty( $this->typography )) {
                $families = array();
                foreach ($this->typography as $key => $value) {
                    $families[] = $key;
                }

                ?>
                <style>.wf-loading *, .wf-inactive * {
                        visibility : hidden;
                    }

                    .wf-active * {
                        visibility : visible;
                    }</style>
                <script>
                    /* You can add more configuration options to webfontloader by previously defining the WebFontConfig with your options */
                    if ( typeof WebFontConfig === "undefined" ) {
                        WebFontConfig = {};
                    }
                    WebFontConfig['google'] = {families: [<?php echo $typography->makeGoogleWebfontString( $this->typography )?>]};

                    (function () {
                        var wf = document.createElement( 'script' );
                        wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.5.0/webfont.js';
                        wf.type = 'text/javascript';
                        wf.async = 'true';
                        var s = document.getElementsByTagName( 'script' )[0];
                        s.parentNode.insertBefore( wf, s );
                    })();
                </script>
            <?php
            } else {
                $protocol = ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) ? "https:" : "http:";

                //echo '<link rel="stylesheet" id="options-google-fonts" title="" href="'.$protocol.$typography->makeGoogleWebfontLink( $this->typography ).'&amp;v='.$version.'" type="text/css" media="all" />';
                wp_register_style(
                    'redux-google-fonts',
                    $protocol . $typography->makeGoogleWebfontLink( $this->typography ),
                    '',
                    $version
                );
                wp_enqueue_style( 'redux-google-fonts' );
            }
        }

    }

    /**
     * Enqueue CSS/JS for options page
     * @global      $wp_styles
     * @return      void
     */
    public function _enqueue()
    {
        global $wp_styles;

        // Select2 business.  Fields:  Background, Border, Dimensions, Select, Slider, Typography
        if (OptionUtil::isFieldInUseByType(
            $this->getFields(),
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
                self::$_url . 'assets/js/vendor/select2/select2.css',
                array(),
                filemtime( self::$_dir . 'assets/js/vendor/select2/select2.css' ),
                'all'
            );

            wp_enqueue_style( 'select2-css' );

            // JS
            wp_register_script(
                'select2-sortable-js',
                self::$_url . 'assets/js/vendor/select2.sortable.min.js',
                array( 'jquery' ),
                filemtime( self::$_dir . 'assets/js/vendor/select2.sortable.min.js' ),
                true
            );

            wp_register_script(
                'select2-js',
                self::$_url . 'assets/js/vendor/select2/select2.min.js',
                array( 'jquery', 'select2-sortable-js' ),
                filemtime( self::$_dir . 'assets/js/vendor/select2/select2.min.js' ),
                true
            );

            wp_enqueue_script( 'select2-js' );
        }

        wp_register_style(
            'redux-css',
            self::$_url . 'assets/css/redux.css',
            array( 'farbtastic' ),
            filemtime( self::$_dir . 'assets/css/redux.css' ),
            'all'
        );

        wp_register_style(
            'admin-css',
            self::$_url . 'assets/css/admin.css',
            array( 'farbtastic' ),
            filemtime( self::$_dir . 'assets/css/admin.css' ),
            'all'
        );

        wp_register_style(
            'redux-elusive-icon',
            self::$_url . 'assets/css/vendor/elusive-icons/elusive-webfont.css',
            array(),
            filemtime( self::$_dir . 'assets/css/vendor/elusive-icons/elusive-webfont.css' ),
            'all'
        );

        wp_register_style(
            'redux-elusive-icon-ie7',
            self::$_url . 'assets/css/vendor/elusive-icons/elusive-webfont-ie7.css',
            array(),
            filemtime( self::$_dir . 'assets/css/vendor/elusive-icons/elusive-webfont-ie7.css' ),
            'all'
        );

        wp_register_style(
            'qtip-css',
            self::$_url . 'assets/css/vendor/qtip/jquery.qtip.css',
            array(),
            filemtime( self::$_dir . 'assets/css/vendor/qtip/jquery.qtip.css' ),
            'all'
        );

        $wp_styles->add_data( 'redux-elusive-icon-ie7', 'conditional', 'lte IE 7' );

        /**
         * jQuery UI stylesheet src
         * @param string  bundled stylesheet src
         */
        wp_register_style(
            'jquery-ui-css',
            apply_filters(
                "redux/page/{$this->params['opt_name']}/enqueue/jquery-ui-css",
                self::$_url . 'assets/css/vendor/jquery-ui-bootstrap/jquery-ui-1.10.0.custom.css'
            ),
            '',
            filemtime( self::$_dir . 'assets/css/vendor/jquery-ui-bootstrap/jquery-ui-1.10.0.custom.css' ),
            // todo - version should be based on above post-filter src
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
                self::$_url . 'assets/css/rtl.css',
                '',
                filemtime( self::$_dir . 'assets/css/rtl.css' ),
                'all'
            );
            wp_enqueue_style( 'redux-rtl-css' );
        }

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-dialog' );

        // Load jQuery sortable for slides, sorter, sortable and group
        if (OptionUtil::isFieldInUseByType(
            $this->getFields(),
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
        if (OptionUtil::isFieldInUseByType( $this->getFields(), array( 'date' ) )) {
            wp_enqueue_script( 'jquery-ui-datepicker' );
        }

        // Load jQuery UI Accordion for slides and group
        if (OptionUtil::isFieldInUseByType( $this->getFields(), array( 'slides', 'group' ) )) {
            wp_enqueue_script( 'jquery-ui-accordion' );
        }

        // Load wp-color-picker for color, color_gradient, link_color, border, background and typography
        if (OptionUtil::isFieldInUseByType(
            $this->getFields(),
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
                self::$_url . 'assets/css/color-picker/color-picker.css',
                array(),
                filemtime( self::$_dir . 'assets/css/color-picker/color-picker.css' ),
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
            self::$_url . 'assets/js/vendor/qtip/jquery.qtip.js',
            array( 'jquery' ),
            '2.2.0',
            true
        );

        wp_register_script(
            'serializeForm-js',
            self::$_url . 'assets/js/vendor/jquery.serializeForm.js',
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
                self::$_url . 'assets/js/vendor.min.js',
                array( 'jquery' ),
                filemtime( self::$_dir . 'assets/js/vendor.min.js' ),
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
            self::$_url . 'assets/js/redux.js',
            $depArray,
            filemtime( self::$_dir . 'assets/js/redux.js' ),
            true
        );

        foreach ($this->getSections() as $section) {
            if (isset( $section['fields'] )) {
                foreach ($section['fields'] as $field) {
                    if (!isset( $field['type'] ) || $field['type'] == 'callback') {
                        continue;
                    }

                    $field_class = "Mozart\\Component\\Form\\Field\\" . Str::camel( $field['type'] );

                    if (false === class_exists( $field_class )) {
                        if (false === class_exists( $field_class . 'Field' )) {
                            continue;
                        } else {
                            $field_class = $field_class . 'Field';
                        }
                    }

                    if (false === method_exists( $field_class, 'enqueue' )
                        && false === method_exists( $field_class, 'localize' )
                    ) {
                        continue;
                    }
                    if (!isset( $this->options[$field['id']] )) {
                        $this->options[$field['id']] = "";
                    }
                    $theField = new $field_class( $field, $this->options[$field['id']], $this );

                    // Move dev_mode check to a new if/then block
                    if (!wp_script_is(
                            'redux-field-' . $field['type'] . '-js',
                            'enqueued'
                        ) && class_exists( $field_class ) && method_exists( $field_class, 'enqueue' )
                    ) {
                        $theField->enqueue();
                    }

                    if (method_exists( $field_class, 'localize' )) {
                        $params = $theField->localize( $field );
                        if (!isset( $this->localize_data[$field['type']] )) {
                            $this->localize_data[$field['type']] = array();
                        }
                        $this->localize_data[$field['type']][$field['id']] = $theField->localize( $field );
                    }

                    unset( $theField );
                }
            }
        }

        $this->localize_data['required'] = $this->required;
        $this->localize_data['fonts'] = $this->fonts;
        $this->localize_data['required_child'] = $this->required_child;
        $this->localize_data['fields'] = $this->getFields();

        if (isset( $this->font_groups['google'] )) {
            $this->localize_data['googlefonts'] = $this->font_groups['google'];
        }

        if (isset( $this->font_groups['std'] )) {
            $this->localize_data['stdfonts'] = $this->font_groups['std'];
        }

        if (isset( $this->font_groups['customfonts'] )) {
            $this->localize_data['customfonts'] = $this->font_groups['customfonts'];
        }

        $this->localize_data['folds'] = $this->folds;

        // Make sure the children are all hidden properly.
        foreach ($this->getFields() as $key => $value) {
            if (in_array( $key, $this->fieldsHidden )) {
                foreach ($value as $k => $v) {
                    if (!in_array( $k, $this->fieldsHidden )) {
                        $this->fieldsHidden[] = $k;
                        $this->folds[$k] = "hide";
                    }
                }
            }
        }

        $this->localize_data['fieldsHidden'] = $this->fieldsHidden;
        $this->localize_data['options'] = $this->options;
        $this->localize_data['defaults'] = $this->options_defaults;

        $save_pending = __( 'You have changes that are not saved. Would you like to save them now?', 'mozart-options' );
        $reset_all = __( 'Are you sure? Resetting will lose all custom values.', 'mozart-options' );
        $reset_section = __( 'Are you sure? Resetting will lose all custom values in this section.', 'mozart-options' );
        $preset_confirm = ___(
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

    /**
     * Show page help
     * @return      void
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
     * @return      string $this->params['footer_credit']
     */
    public function admin_footer_text()
    {
        return $this->params['footer_credit'];
    }

    /**
     * Return default output string for use in panel
     *
     * @return      string default_output
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
     * @return      void
     */
    public function _register_settings()
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

        if (is_null( $this->getSections() )) {
            return;
        }

        $this->options_defaults = $this->getDefaultOptions();

        $runUpdate = false;

        foreach ($this->getSections() as $k => $section) {
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

            /**
             * @param array $section section configuration
             */
            $section = apply_filters( "redux-section-{$k}-modifier-{$this->params['opt_name']}", $section );

            /**
             * @param array $section section configuration
             */
            if (isset( $section['id'] )) {
                $section = apply_filters(
                    "redux/options/{$this->params['opt_name']}/section/{$section['id']}",
                    $section
                );
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

                            $this->hidden_perm_fields[$field_id] = $data;
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

                    /**
                     * @param array $field field config
                     */
                    $field = apply_filters(
                        "redux/options/{$this->params['opt_name']}/field/{$field['id']}/register",
                        $field
                    );

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

                            $this->hidden_perm_fields[$field['id']] = $data;

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

                    if (!isset( $field['class'] )) { // No errors please
                        $field['class'] = "";
                    }
                    $id = $field['id'];

                    /**
                     * @param array $field field config
                     */
                    $field = apply_filters( "redux/options/{$this->params['opt_name']}/field/{$field['id']}", $field );

                    if (empty( $field ) || !$field || $field == false) {
                        unset( $this->sections[$k]['fields'][$fieldk] );
                        continue;
                    }

                    if (!empty( $this->folds[$field['id']]['parent'] )) { // This has some fold items, hide it by default
                        $field['class'] .= " fold";
                    }

                    if (!empty( $this->folds[$field['id']]['children'] )) { // Sets the values you shoe fold children on
                        $field['class'] .= " foldParent";
                    }

                    if (!empty( $field['compiler'] )) {
                        $field['class'] .= " compiler";
                        $this->compiler_fields[$field['id']] = 1;
                    }

                    if (isset( $field['unit'] ) && !isset( $field['units'] )) {
                        $field['units'] = $field['unit'];
                        unset( $field['unit'] );
                    }

                    $this->sections[$k]['fields'][$fieldk] = $field;

                    if (isset( $this->params['display_source'] )) {
                        $th .= '<div id="' . $field['id'] . '-settings" style="display:none;"><pre>' . var_export(
                                $this->sections[$k]['fields'][$fieldk],
                                true
                            ) . '</pre></div>';
                        $th .= '<br /><a href="#TB_inline?width=600&height=800&inlineId=' . $field['id'] . '-settings" class="thickbox"><small>View Source</small></a>';
                    }

                    $this->check_dependencies( $field );

                    add_settings_field(
                        "{$fieldk}_field",
                        $th,
                        array( &$this, '_field_input' ),
                        "{$this->params['opt_name']}{$k}_section_group",
                        "{$this->params['opt_name']}{$k}_section",
                        $field
                    ); // checkbox
                }
            }
        }

        if ($runUpdate && !isset( $this->never_save_to_db )) { // Always update the DB with new fields
            $this->setOptions( $this->options );
        }

        if (isset( $this->transients['run_compiler'] ) && $this->transients['run_compiler']) {
            $this->params['output_tag'] = false;
            $this->_enqueue_output();

            unset( $this->transients['run_compiler'] );
            $this->set_transients();
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
    public function set_transients()
    {
        if (!isset( $this->transients ) || !isset( $this->transients_check ) || $this->transients != $this->transients_check) {
            update_option( $this->params['opt_name'] . '-transients', $this->transients );
            $this->transients_check = $this->transients;
        }
    }

    /**
     * Validate the Options options before insertion
     *
     * @param       array $plugin_options The options array
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
                $this->set_transients();

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

            $this->set_transients(); // Update the transients

            return $plugin_options;
        }

        // Section reset to defaults
        if (!empty( $plugin_options['defaults-section'] )) {
            if (isset( $plugin_options['redux-section'] ) && isset( $this->sections[$plugin_options['redux-section']]['fields'] )) {
                foreach ($this->sections[$plugin_options['redux-section']]['fields'] as $field) {
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
            $this->set_transients();

            return $plugin_options;
        }

        $this->transients['last_save_mode'] = "normal"; // Last save mode

        // Validate fields (if needed)
        $plugin_options = $this->_validate_values( $plugin_options, $this->options, $this->getSections() );

        if (!empty( $this->errors ) || !empty( $this->warnings )) {
            $this->transients['notices'] = array( 'errors' => $this->errors, 'warnings' => $this->warnings );
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

            return;
        }

        if (defined( 'WP_CACHE' ) && WP_CACHE && class_exists( 'W3_ObjectCache' )) {
            $w3 = W3_ObjectCache::instance();
            $key = $w3->_get_cache_key( $this->params['opt_name'] . '-transients', 'transient' );
            $w3->delete( $key, 'transient', true );
        }

        $this->set_transients( $this->transients );

        return $plugin_options;
    }

    /**
     * Validate values from options form (used in settings api validate function)
     * calls the custom validation class for the field so authors can override with custom classes
     *
     * @param       array $plugin_options
     * @param       array $options
     *
     * @return      array $plugin_options
     */
    public function _validate_values( $plugin_options, $options, $sections )
    {
        foreach ($sections as $k => $section) {
            if (isset( $section['fields'] )) {
                foreach ($section['fields'] as $fkey => $field) {
                    $field['section_id'] = $k;

                    if (isset( $field['type'] ) && ( $field['type'] == 'checkbox' || $field['type'] == 'checkbox_hide_below' || $field['type'] == 'checkbox_hide_all' )) {
                        if (!isset( $plugin_options[$field['id']] )) {
                            $plugin_options[$field['id']] = 0;
                        }
                    }

                    // Default 'not_empty 'flag to false.
                    $isNotEmpty = false;

                    // Make sure 'validate' field is set.
                    if (isset( $field['validate'] )) {

                        // Make sure 'validate field' is set to 'not_empty' or 'email_not_empty'
                        if ($field['validate'] == 'not_empty' || $field['validate'] == 'email_not_empty' || $field['validate'] == 'numeric_not_empty') {

                            // Set the flag.
                            $isNotEmpty = true;
                        }
                    }

                    // Check for empty id value
                    if (!isset( $plugin_options[$field['id']] ) || $plugin_options[$field['id']] == '') {

                        // If we are looking for an empty value, in the case of 'not_empty'
                        // then we need to keep processing.
                        if (!$isNotEmpty) {

                            // Empty id and not checking for 'not_empty.  Bail out...
                            continue;
                        }
                    }

                    // Force validate of custom field types
                    if (isset( $field['type'] ) && !isset( $field['validate'] )) {
                        if ($field['type'] == 'color' || $field['type'] == 'color_gradient') {
                            $field['validate'] = 'color';
                        } elseif ($field['type'] == 'date') {
                            $field['validate'] = 'date';
                        }
                    }

                    if (isset( $field['validate'] )) {
                        $validate = 'Redux_Validation_' . $field['validate'];

                        if (!class_exists( $validate )) {

                            $class_file = self::$_dir . "src/validation/{$field['validate']}/validation_{$field['validate']}.php";

                            if ($class_file) {
                                if (file_exists( $class_file )) {
                                    require_once( $class_file );
                                }
                            }
                        }

                        if (class_exists( $validate )) {

                            if (empty ( $options[$field['id']] )) {
                                $options[$field['id']] = '';
                            }

                            if (isset( $plugin_options[$field['id']] ) && is_array(
                                    $plugin_options[$field['id']]
                                ) && !empty( $plugin_options[$field['id']] )
                            ) {
                                foreach ($plugin_options[$field['id']] as $key => $value) {
                                    $before = $after = null;
                                    if (isset( $plugin_options[$field['id']][$key] ) && !empty( $plugin_options[$field['id']][$key] )) {
                                        if (is_array( $plugin_options[$field['id']][$key] )) {
                                            $before = $plugin_options[$field['id']][$key];
                                        } else {
                                            $before = trim( $plugin_options[$field['id']][$key] );
                                        }
                                    }

                                    if (isset( $options[$field['id']][$key] ) && !empty( $options[$field['id']][$key] )) {
                                        $after = $options[$field['id']][$key];
                                    }

                                    $validation = new $validate( $this, $field, $before, $after );
                                    if (!empty( $validation->value )) {
                                        $plugin_options[$field['id']][$key] = $validation->value;
                                    } else {
                                        unset( $plugin_options[$field['id']][$key] );
                                    }

                                    if (isset( $validation->error )) {
                                        $this->errors[] = $validation->error;
                                    }

                                    if (isset( $validation->warning )) {
                                        $this->warnings[] = $validation->warning;
                                    }
                                }
                            } else {
                                if (is_array( $plugin_options[$field['id']] )) {
                                    $pofi = $plugin_options[$field['id']];
                                } else {
                                    $pofi = trim( $plugin_options[$field['id']] );
                                }

                                $validation = new $validate( $this, $field, $pofi, $options[$field['id']] );
                                $plugin_options[$field['id']] = $validation->value;

                                if (isset( $validation->error )) {
                                    $this->errors[] = $validation->error;
                                }

                                if (isset( $validation->warning )) {
                                    $this->warnings[] = $validation->warning;
                                }
                            }

                            continue;
                        }
                    }

                    if (isset( $field['validate_callback'] ) && function_exists( $field['validate_callback'] )) {
                        $callbackvalues = call_user_func(
                            $field['validate_callback'],
                            $field,
                            $plugin_options[$field['id']],
                            $options[$field['id']]
                        );
                        $plugin_options[$field['id']] = $callbackvalues['value'];

                        if (isset( $callbackvalues['error'] )) {
                            $this->errors[] = $callbackvalues['error'];
                        }

                        if (isset( $callbackvalues['warning'] )) {
                            $this->warnings[] = $callbackvalues['warning'];
                        }
                    }
                }
            }
        }

        return $plugin_options;
    }

    /**
     * Return Section Menu HTML
     *
     * @return      void
     */
    public function section_menu( $k, $section, $suffix = "", $sections = array() )
    {
        $display = true;

        $section['class'] = isset( $section['class'] ) ? ' ' . $section['class'] : '';

        if (isset( $_GET['page'] ) && $_GET['page'] == $this->params['page_slug']) {
            if (isset( $section['panel'] ) && $section['panel'] == false) {
                $display = false;
            }
        }

        if (!$display) {
            return "";
        }

        if (empty( $sections )) {
            $sections = $this->getSections();
        }

        $string = "";
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

            // DOVY! REPLACE $k with $section['ID'] when used properly.
            //$active = ( ( is_numeric($this->current_tab) && $this->current_tab == $k ) || ( !is_numeric($this->current_tab) && $this->current_tab === $k )  ) ? ' active' : '';
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
     * @return      void
     */
    public
    function _options_page_html()
    {
        echo '<div class="wrap"><h2></h2></div>'; // Stupid hack for Wordpress alerts and warnings

        echo '<div class="clear"></div>';
        echo '<div class="wrap">';

        // Do we support JS?
        echo '<noscript><div class="no-js">' . __(
                'Warning- This options panel will not work properly without javascript!',
                'mozart-options'
            ) . '</div></noscript>';

        // Security is vital!
        echo '<input type="hidden" id="ajaxsecurity" name="security" value="' . wp_create_nonce(
                'redux_ajax_nonce'
            ) . '" />';

        // Main container
        $expanded = ( $this->params['open_expanded'] ) ? ' fully-expanded' : '';

        echo '<div class="redux-container' . $expanded . ( !empty( $this->params['class'] ) ? ' ' . $this->params['class'] : '' ) . '">';
        $url = './options.php';
        if ($this->params['database'] == "network" && $this->params['network_admin']) {
            if (is_network_admin()) {
                $url = './edit.php?action=redux_' . $this->params['opt_name'];
            }
        }
        echo '<form method="post" action="' . $url . '" enctype="multipart/form-data" id="redux-form-wrapper">';
        echo '<input type="hidden" id="redux-compiler-hook" name="' . $this->params['opt_name'] . '[compiler]" value="" />';
        echo '<input type="hidden" id="currentSection" name="' . $this->params['opt_name'] . '[redux-section]" value="" />';

        settings_fields( "{$this->params['opt_name']}_group" );

        // Last tab?
        $this->options['last_tab'] = ( isset( $_GET['tab'] ) && !isset( $this->transients['last_save_mode'] ) ) ? $_GET['tab'] : '';

        echo '<input type="hidden" id="last_tab" name="' . $this->params['opt_name'] . '[last_tab]" value="' . $this->options['last_tab'] . '" />';

        // Header area
        echo '<div id="redux-header">';

        if (!empty( $this->params['display_name'] )) {
            echo '<div class="display_header">';
            echo '<h2>' . $this->params['display_name'] . '</h2>';

            if (!empty( $this->params['display_version'] )) {
                echo '<span>' . $this->params['display_version'] . '</span>';
            }

            echo '</div>';
        }

        // Page icon
        echo '<div id="' . $this->params['page_icon'] . '" class="icon32"></div>';

        echo '<div class="clear"></div>';
        echo '</div>';

        // Intro text
        if (isset( $this->params['intro_text'] )) {
            echo '<div id="redux-intro-text">';
            echo $this->params['intro_text'];
            echo '</div>';
        }

        // Stickybar
        echo '<div id="redux-sticky">';
        echo '<div id="info_bar">';

        $expanded = ( $this->params['open_expanded'] ) ? ' expanded' : '';

        echo '<a href="javascript:void(0);" class="expand_options' . $expanded . '">' . __(
                'Expand',
                'mozart-options'
            ) . '</a>';
        echo '<div class="redux-action_bar">';
        submit_button( __( 'Save Changes', 'mozart-options' ), 'primary', 'redux_save', false );

        if (false === $this->params['hide_reset']) {
            echo '&nbsp;';
            submit_button(
                __( 'Reset Section', 'mozart-options' ),
                'secondary',
                $this->params['opt_name'] . '[defaults-section]',
                false
            );
            echo '&nbsp;';
            submit_button(
                __( 'Reset All', 'mozart-options' ),
                'secondary',
                $this->params['opt_name'] . '[defaults]',
                false
            );
        }

        echo '</div>';

        echo '<div class="redux-ajax-loading" alt="' . __( 'Working...', 'mozart-options' ) . '">&nbsp;</div>';
        echo '<div class="clear"></div>';
        echo '</div>';

        // Warning bar
        if (isset( $this->transients['last_save_mode'] )) {

            if ($this->transients['last_save_mode'] == "import") {
                echo '<div class="admin-notice notice-blue saved_notice"><strong>' . apply_filters(
                        "redux-imported-text-{$this->params['opt_name']}",
                        __( 'Settings Imported!', 'mozart-options' )
                    ) . '</strong></div>';
            } elseif ($this->transients['last_save_mode'] == "defaults") {
                echo '<div class="saved_notice admin-notice notice-yellow"><strong>' . apply_filters(
                        "redux-defaults-text-{$this->params['opt_name']}",
                        __( 'All Defaults Restored!', 'mozart-options' )
                    ) . '</strong></div>';
            } elseif ($this->transients['last_save_mode'] == "defaults_section") {

                echo '<div class="saved_notice admin-notice notice-yellow"><strong>' . apply_filters(
                        "redux-defaults-section-text-{$this->params['opt_name']}",
                        __( 'Section Defaults Restored!', 'mozart-options' )
                    ) . '</strong></div>';
            } else {
                echo '<div class="saved_notice admin-notice notice-green"><strong>' . apply_filters(
                        "redux-saved-text-{$this->params['opt_name']}",
                        __( 'Settings Saved!', 'mozart-options' )
                    ) . '</strong></div>';
            }
            unset( $this->transients['last_save_mode'] );

        }

        echo '<div class="redux-save-warn notice-yellow"><strong>' . apply_filters(
                "redux-changed-text-{$this->params['opt_name']}",
                __( 'Settings have changed, you should save them!', 'mozart-options' )
            ) . '</strong></div>';

        echo '<div class="redux-field-errors notice-red"><strong><span></span> ' . __(
                'error(s) were found!',
                'mozart-options'
            ) . '</strong></div>';

        echo '<div class="redux-field-warnings notice-yellow"><strong><span></span> ' . __(
                'warning(s) were found!',
                'mozart-options'
            ) . '</strong></div>';

        echo '</div>';

        echo '<div class="clear"></div>';

        // Sidebar
        echo '<div class="redux-sidebar">';
        echo '<ul class="redux-group-menu">';

        foreach ($this->getSections() as $k => $section) {
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

            if (false == $skip_sec) {
                echo $this->section_menu( $k, $section );
                $skip_sec = false;
            }
        }

        // Import / Export tab
        if (true == $this->params['show_importer'] && false == $this->importer->is_field) {
            $this->importer->render_tab();
        }

        // Debug tab
        if ($this->params['dev_mode'] == true) {
            $this->debugger->render_tab();
        }

        if ($this->params['system_info'] === true) {
            echo '<li id="system_info_default_section_group_li" class="redux-group-tab-link-li">';

            if (!empty( $this->params['icon_type'] ) && $this->params['icon_type'] == 'image') {
                $icon = ( !isset( $this->params['system_info_icon'] ) ) ? '' : '<img src="' . $this->params['system_info_icon'] . '" /> ';
            } else {
                $icon_class = ( !isset( $this->params['system_info_icon_class'] ) ) ? '' : ' ' . $this->params['system_info_icon_class'];
                $icon = ( !isset( $this->params['system_info_icon'] ) ) ? '<i class="el-icon-info-sign' . $icon_class . '"></i>' : '<i class="icon-' . $this->params['system_info_icon'] . $icon_class . '"></i> ';
            }

            echo '<a href="javascript:void(0);" id="system_info_default_section_group_li_a" class="redux-group-tab-link-a custom-tab" data-rel="system_info_default">' . $icon . ' <span class="group_title">' . __(
                    'System Info',
                    'mozart-options'
                ) . '</span></a>';
            echo '</li>';
        }

        echo '</ul>';
        echo '</div>';

        echo '<div class="redux-main">';

        foreach ($this->getSections() as $k => $section) {
            if (isset( $section['customizer_only'] ) && $section['customizer_only'] == true) {
                continue;
            }

            $section['class'] = isset( $section['class'] ) ? ' ' . $section['class'] : '';
            echo '<div id="' . $k . '_section_group' . '" class="redux-group-tab' . $section['class'] . '" data-rel="' . $k . '">';

            // Don't display in the
            $display = true;
            if (isset( $_GET['page'] ) && $_GET['page'] == $this->params['page_slug']) {
                if (isset( $section['panel'] ) && $section['panel'] == "false") {
                    $display = false;
                }
            }

            if ($display) {
                do_settings_sections( $this->params['opt_name'] . $k . '_section_group' );
            }
            echo "</div>";
        }

        // Import / Export output
        if (true == $this->params['show_importer'] && false == $this->importer->is_field) {
            $this->importer->enqueue();

            echo '<fieldset id="' . $this->params['opt_name'] . '-importer_core" class="redux-field-container redux-field redux-field-init redux-container-importer" data-id="importer_core" data-type="importer">';
            $this->importer->render();
            echo '</fieldset>';

        }

        // Debug object output
        if ($this->params['dev_mode'] == true) {
            $this->debugger->render();
        }

        if ($this->params['system_info'] === true) {
            echo '<div id="system_info_default_section_group' . '" class="redux-group-tab">';
            echo '<h3>' . __( 'System Info', 'mozart-options' ) . '</h3>';

            echo '<div id="redux-system-info">';
            echo SystemInfo::get();
            echo '</div>';

            echo '</div>';
        }

        echo '<div class="clear"></div>';
        echo '</div>';
        echo '<div class="clear"></div>';

        echo '<div id="redux-sticky-padder" style="display: none;">&nbsp;</div>';
        echo '<div id="redux-footer-sticky"><div id="redux-footer">';

        if (isset( $this->params['share_icons'] )) {
            echo '<div id="redux-share">';

            foreach ($this->params['share_icons'] as $link) {
                // SHIM, use URL now
                if (isset( $link['link'] ) && !empty( $link['link'] )) {
                    $link['url'] = $link['link'];
                    unset( $link['link'] );
                }

                echo '<a href="' . $link['url'] . '" title="' . $link['title'] . '" target="_blank">';

                if (isset( $link['icon'] ) && !empty( $link['icon'] )) {
                    echo '<i class="' . $link['icon'] . '"></i>';
                } else {
                    echo '<img src="' . $link['img'] . '"/>';
                }

                echo '</a>';
            }

            echo '</div>';
        }

        echo '<div class="redux-action_bar">';
        submit_button( __( 'Save Changes', 'mozart-options' ), 'primary', 'redux_save', false );

        if (false === $this->params['hide_reset']) {
            echo '&nbsp;';
            submit_button(
                __( 'Reset Section', 'mozart-options' ),
                'secondary',
                $this->params['opt_name'] . '[defaults-section]',
                false
            );
            echo '&nbsp;';
            submit_button(
                __( 'Reset All', 'mozart-options' ),
                'secondary',
                $this->params['opt_name'] . '[defaults]',
                false
            );
        }

        echo '</div>';

        echo '<div class="redux-ajax-loading" alt="' . __( 'Working...', 'mozart-options' ) . '">&nbsp;</div>';
        echo '<div class="clear"></div>';

        echo '</div>';
        echo '</form>';
        echo '</div></div>';

        echo ( isset( $this->params['footer_text'] ) ) ? '<div id="redux-sub-footer">' . $this->params['footer_text'] . '</div>' : '';


        echo '<div class="clear"></div>';
        echo '</div><!--wrap-->';

        if ($this->params['dev_mode'] == true) {
            if (current_user_can( 'administrator' )) {
                global $wpdb;
                echo "<br /><pre>";
                print_r( $wpdb->queries );
                echo "</pre>";
            }

            echo '<br /><div class="redux-timer">' . get_num_queries() . ' queries in ' . timer_stop(
                    0
                ) . ' seconds</div>';
        }

        $this->set_transients();

    }

    /**
     * Section HTML OUTPUT.
     *
     * @param       array $section
     *
     * @return      void
     */
    public
    function _section_desc(
        $section
    ) {
        $id = trim( rtrim( $section['id'], '_section' ), $this->params['opt_name'] );

        if (isset( $this->sections[$id]['desc'] ) && !empty( $this->sections[$id]['desc'] )) {
            echo '<div class="redux-section-desc">' . $this->sections[$id]['desc'] . '</div>';
        }
    }

    /**
     * Field HTML OUTPUT.
     * Gets option from options array, then calls the specific field type class - allows extending by other devs
     *
     * @param array $field
     * @param string $v
     *
     * @return      void
     */
    public
    function _field_input(
        $field,
        $v = null
    ) {
        if (isset( $field['callback'] ) && function_exists( $field['callback'] )) {
            $value = ( isset( $this->options[$field['id']] ) ) ? $this->options[$field['id']] : '';
            call_user_func( $field['callback'], $field, $value );

            return;
        }

        if (isset( $field['type'] )) {

            // If the field is set not to display in the panel
            $display = true;
            if (isset( $_GET['page'] ) && $_GET['page'] == $this->params['page_slug']) {
                if (isset( $field['panel'] ) && $field['panel'] == false) {
                    $display = false;
                }
            }

            if (!$display) {
                return;
            }

            $field_class = "Mozart\\Component\\Form\\Field\\" . Str::camel( $field['type'] );

            if (false === class_exists( $field_class )) {
                if (false === class_exists( $field_class . 'Field' )) {
                    return false;
                }
            }

            $value = isset( $this->options[$field['id']] ) ? $this->options[$field['id']] : '';

            if ($v !== null) {
                $value = $v;
            }

            if (!isset( $field['name_suffix'] )) {
                $field['name_suffix'] = "";
            }

            $render = new $field_class( $field, $value, $this );
            ob_start();

            $render->render();

            /**
             * @param string $_render rendered field markup
             * @param array $field field data
             */
            $_render = apply_filters(
                "redux/field/{$this->params['opt_name']}/{$field['type']}/render/after",
                $render,
                $field
            );

            ob_end_clean();

            //save the values into a unique array in case we need it for dependencies
            $this->fieldsValues[$field['id']] = ( isset( $value['url'] ) && is_array(
                    $value
                ) ) ? $value['url'] : $value;

            //create default data und class string and checks the dependencies of an object
            $class_string = '';
            $data_string = '';

            $this->check_dependencies( $field );

            if (!isset( $field['fields'] ) || empty( $field['fields'] )) {
                echo '<fieldset id="' . $this->params['opt_name'] . '-' . $field['id'] . '" class="redux-field-container redux-field redux-field-init redux-container-' . $field['type'] . ' ' . $class_string . '" data-id="' . $field['id'] . '" ' . $data_string . ' data-type="' . $field['type'] . '">';
            }

            echo $_render;

            if (!empty( $field['desc'] )) {
                $field['description'] = $field['desc'];
            }

            echo ( isset( $field['description'] ) && $field['type'] != "info" && $field['type'] !== "section" && !empty( $field['description'] ) ) ? '<div class="description field-desc">' . $field['description'] . '</div>' : '';

            if (!isset( $field['fields'] ) || empty( $field['fields'] )) {
                echo '</fieldset>';
            }
        }
    }

    /**
     * Can Output CSS
     * Check if a field meets its requirements before outputting to CSS
     *
     * @param $field
     *
     * @return bool
     */
    public function _can_output_css( $field )
    {
        $return = true;

        if (isset( $field['force_output'] ) && $field['force_output'] == true) {
            return $return;
        }

        if (!empty( $field['required'] )) {
            if (isset( $field['required'][0] )) {
                if (!is_array( $field['required'][0] ) && count( $field['required'] ) == 3) {
                    $parentValue = $GLOBALS[$this->params['global_variable']][$field['required'][0]];
                    $checkValue = $field['required'][2];
                    $operation = $field['required'][1];
                    $return = $this->compareValueDependencies( $parentValue, $checkValue, $operation );
                } elseif (is_array( $field['required'][0] )) {
                    foreach ($field['required'] as $required) {
                        if (!is_array( $required[0] ) && count( $required ) == 3) {
                            $parentValue = $GLOBALS[$this->params['global_variable']][$required[0]];
                            $checkValue = $required[2];
                            $operation = $required[1];
                            $return = $this->compareValueDependencies( $parentValue, $checkValue, $operation );
                        }
                        if (!$return) {
                            return $return;
                        }
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Checks dependencies between objects based on the $field['required'] array
     * If the array is set it needs to have exactly 3 entries.
     * The first entry describes which field should be monitored by the current field. eg: "content"
     * The second entry describes the comparison parameter. eg: "equals, not, is_larger, is_smaller ,contains"
     * The third entry describes the value that we are comparing against.
     * Example: if the required array is set to array('content','equals','Hello World'); then the current
     * field will only be displayed if the field with id "content" has exactly the value "Hello World"
     *
     * @param array $field
     *
     * @return array $params
     */
    public
    function check_dependencies(
        $field
    ) {
        if (!empty( $field['required'] )) {

            //$this->folds[$field['id']] = $this->folds[$field['id']] ? $this->folds[$field['id']] : array();
            if (!isset( $this->required_child[$field['id']] )) {
                $this->required_child[$field['id']] = array();
            }

            if (!isset( $this->required[$field['id']] )) {
                $this->required[$field['id']] = array();
            }

            if (is_array( $field['required'][0] )) {
                foreach ($field['required'] as $value) {
                    if (is_array( $value ) && count( $value ) == 3) {
                        $data = array();
                        $data['parent'] = $value[0];
                        $data['operation'] = $value[1];
                        $data['checkValue'] = $value[2];

                        $this->required[$data['parent']][$field['id']][] = $data;

                        if (!in_array( $data['parent'], $this->required_child[$field['id']] )) {
                            $this->required_child[$field['id']][] = $data;
                        }

                        $this->checkRequiredDependencies( $field, $data );
                    }
                }
            } else {
                $data = array();
                $data['parent'] = $field['required'][0];
                $data['operation'] = $field['required'][1];
                $data['checkValue'] = $field['required'][2];

                $this->required[$data['parent']][$field['id']][] = $data;

                if (!in_array( $data['parent'], $this->required_child[$field['id']] )) {
                    $this->required_child[$field['id']][] = $data;
                }

                $this->checkRequiredDependencies( $field, $data );
            }

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
     * @param $field
     * @param $data
     */
    public function checkRequiredDependencies( $field, $data )
    {
        //required field must not be hidden. otherwise hide this one by default

        if (!in_array(
                $data['parent'],
                $this->fieldsHidden
            ) && ( !isset( $this->folds[$field['id']] ) || $this->folds[$field['id']] != "hide" )
        ) {
            if (isset( $this->options[$data['parent']] )) {
                $return = $this->compareValueDependencies(
                    $this->options[$data['parent']],
                    $data['checkValue'],
                    $data['operation']
                );
            }
        }

        if (( isset( $return ) && $return ) && ( !isset( $this->folds[$field['id']] ) || $this->folds[$field['id']] != "hide" )) {
            $this->folds[$field['id']] = "show";
        } else {
            $this->folds[$field['id']] = "hide";
            if (!in_array( $field['id'], $this->fieldsHidden )) {
                $this->fieldsHidden[] = $field['id'];
            }
        }
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

    private function getFontIcons()
    {
        return array(
            'el-icon-address-book-alt',
            'el-icon-address-book',
            'el-icon-adjust-alt',
            'el-icon-adjust',
            'el-icon-adult',
            'el-icon-align-center',
            'el-icon-align-justify',
            'el-icon-align-left',
            'el-icon-align-right',
            'el-icon-arrow-down',
            'el-icon-arrow-left',
            'el-icon-arrow-right',
            'el-icon-arrow-up',
            'el-icon-asl',
            'el-icon-asterisk',
            'el-icon-backward',
            'el-icon-ban-circle',
            'el-icon-barcode',
            'el-icon-behance',
            'el-icon-bell',
            'el-icon-blind',
            'el-icon-blogger',
            'el-icon-bold',
            'el-icon-book',
            'el-icon-bookmark-empty',
            'el-icon-bookmark',
            'el-icon-braille',
            'el-icon-briefcase',
            'el-icon-broom',
            'el-icon-brush',
            'el-icon-bulb',
            'el-icon-bullhorn',
            'el-icon-calendar-sign',
            'el-icon-calendar',
            'el-icon-camera',
            'el-icon-car',
            'el-icon-caret-down',
            'el-icon-caret-left',
            'el-icon-caret-right',
            'el-icon-caret-up',
            'el-icon-cc',
            'el-icon-certificate',
            'el-icon-check-empty',
            'el-icon-check',
            'el-icon-chevron-down',
            'el-icon-chevron-left',
            'el-icon-chevron-right',
            'el-icon-chevron-up',
            'el-icon-child',
            'el-icon-circle-arrow-down',
            'el-icon-circle-arrow-left',
            'el-icon-circle-arrow-right',
            'el-icon-circle-arrow-up',
            'el-icon-cloud-alt',
            'el-icon-cloud',
            'el-icon-cog-alt',
            'el-icon-cog',
            'el-icon-cogs',
            'el-icon-comment-alt',
            'el-icon-comment',
            'el-icon-compass-alt',
            'el-icon-compass',
            'el-icon-credit-card',
            'el-icon-css',
            'el-icon-dashboard',
            'el-icon-delicious',
            'el-icon-deviantart',
            'el-icon-digg',
            'el-icon-download-alt',
            'el-icon-download',
            'el-icon-dribbble',
            'el-icon-edit',
            'el-icon-eject',
            'el-icon-envelope-alt',
            'el-icon-envelope',
            'el-icon-error-alt',
            'el-icon-error',
            'el-icon-eur',
            'el-icon-exclamation-sign',
            'el-icon-eye-close',
            'el-icon-eye-open',
            'el-icon-facebook',
            'el-icon-facetime-video',
            'el-icon-fast-backward',
            'el-icon-fast-forward',
            'el-icon-female',
            'el-icon-file-alt',
            'el-icon-file-edit-alt',
            'el-icon-file-edit',
            'el-icon-file-new-alt',
            'el-icon-file-new',
            'el-icon-file',
            'el-icon-film',
            'el-icon-filter',
            'el-icon-fire',
            'el-icon-flag-alt',
            'el-icon-flag',
            'el-icon-flickr',
            'el-icon-folder-close',
            'el-icon-folder-open',
            'el-icon-folder-sign',
            'el-icon-folder',
            'el-icon-font',
            'el-icon-fontsize',
            'el-icon-fork',
            'el-icon-forward-alt',
            'el-icon-forward',
            'el-icon-foursquare',
            'el-icon-friendfeed-rect',
            'el-icon-friendfeed',
            'el-icon-fullscreen',
            'el-icon-gbp',
            'el-icon-gift',
            'el-icon-github-text',
            'el-icon-github',
            'el-icon-glass',
            'el-icon-glasses',
            'el-icon-globe-alt',
            'el-icon-globe',
            'el-icon-googleplus',
            'el-icon-graph-alt',
            'el-icon-graph',
            'el-icon-group-alt',
            'el-icon-group',
            'el-icon-guidedog',
            'el-icon-hand-down',
            'el-icon-hand-left',
            'el-icon-hand-right',
            'el-icon-hand-up',
            'el-icon-hdd',
            'el-icon-headphones',
            'el-icon-hearing-impaired',
            'el-icon-heart-alt',
            'el-icon-heart-empty',
            'el-icon-heart',
            'el-icon-home-alt',
            'el-icon-home',
            'el-icon-hourglass',
            'el-icon-idea-alt',
            'el-icon-idea',
            'el-icon-inbox-alt',
            'el-icon-inbox-box',
            'el-icon-inbox',
            'el-icon-indent-left',
            'el-icon-indent-right',
            'el-icon-info-sign',
            'el-icon-instagram',
            'el-icon-iphone-home',
            'el-icon-italic',
            'el-icon-key',
            'el-icon-laptop-alt',
            'el-icon-laptop',
            'el-icon-lastfm',
            'el-icon-leaf',
            'el-icon-lines',
            'el-icon-link',
            'el-icon-linkedin',
            'el-icon-list-alt',
            'el-icon-list',
            'el-icon-livejournal',
            'el-icon-lock-alt',
            'el-icon-lock',
            'el-icon-magic',
            'el-icon-magnet',
            'el-icon-male',
            'el-icon-map-marker-alt',
            'el-icon-map-marker',
            'el-icon-mic-alt',
            'el-icon-mic',
            'el-icon-minus-sign',
            'el-icon-minus',
            'el-icon-move',
            'el-icon-music',
            'el-icon-myspace',
            'el-icon-network',
            'el-icon-off',
            'el-icon-ok-circle',
            'el-icon-ok-sign',
            'el-icon-ok',
            'el-icon-opensource',
            'el-icon-paper-clip-alt',
            'el-icon-paper-clip',
            'el-icon-path',
            'el-icon-pause-alt',
            'el-icon-pause',
            'el-icon-pencil-alt',
            'el-icon-pencil',
            'el-icon-person',
            'el-icon-phone-alt',
            'el-icon-phone',
            'el-icon-photo-alt',
            'el-icon-photo',
            'el-icon-picasa',
            'el-icon-picture',
            'el-icon-pinterest',
            'el-icon-plane',
            'el-icon-play-alt',
            'el-icon-play-circle',
            'el-icon-play',
            'el-icon-plus-sign',
            'el-icon-plus',
            'el-icon-podcast',
            'el-icon-print',
            'el-icon-puzzle',
            'el-icon-qrcode',
            'el-icon-question-sign',
            'el-icon-question',
            'el-icon-quotes-alt',
            'el-icon-quotes',
            'el-icon-random',
            'el-icon-record',
            'el-icon-reddit',
            'el-icon-refresh',
            'el-icon-remove-circle',
            'el-icon-remove-sign',
            'el-icon-remove',
            'el-icon-repeat-alt',
            'el-icon-repeat',
            'el-icon-resize-full',
            'el-icon-resize-horizontal',
            'el-icon-resize-small',
            'el-icon-resize-vertical',
            'el-icon-return-key',
            'el-icon-retweet',
            'el-icon-reverse-alt',
            'el-icon-road',
            'el-icon-rss',
            'el-icon-scissors',
            'el-icon-screen-alt',
            'el-icon-screen',
            'el-icon-screenshot',
            'el-icon-search-alt',
            'el-icon-search',
            'el-icon-share-alt',
            'el-icon-share',
            'el-icon-shopping-cart-sign',
            'el-icon-shopping-cart',
            'el-icon-signal',
            'el-icon-skype',
            'el-icon-slideshare',
            'el-icon-smiley-alt',
            'el-icon-smiley',
            'el-icon-soundcloud',
            'el-icon-speaker',
            'el-icon-spotify',
            'el-icon-stackoverflow',
            'el-icon-star-alt',
            'el-icon-star-empty',
            'el-icon-star',
            'el-icon-step-backward',
            'el-icon-step-forward',
            'el-icon-stop-alt',
            'el-icon-stop',
            'el-icon-stumbleupon',
            'el-icon-tag',
            'el-icon-tags',
            'el-icon-tasks',
            'el-icon-text-height',
            'el-icon-text-width',
            'el-icon-th-large',
            'el-icon-th-list',
            'el-icon-th',
            'el-icon-thumbs-down',
            'el-icon-thumbs-up',
            'el-icon-time-alt',
            'el-icon-time',
            'el-icon-tint',
            'el-icon-torso',
            'el-icon-trash-alt',
            'el-icon-trash',
            'el-icon-tumblr',
            'el-icon-twitter',
            'el-icon-universal-access',
            'el-icon-unlock-alt',
            'el-icon-unlock',
            'el-icon-upload',
            'el-icon-usd',
            'el-icon-user',
            'el-icon-viadeo',
            'el-icon-video-alt',
            'el-icon-video-chat',
            'el-icon-video',
            'el-icon-view-mode',
            'el-icon-vimeo',
            'el-icon-vkontakte',
            'el-icon-volume-down',
            'el-icon-volume-off',
            'el-icon-volume-up',
            'el-icon-w3c',
            'el-icon-warning-sign',
            'el-icon-website-alt',
            'el-icon-website',
            'el-icon-wheelchair',
            'el-icon-wordpress',
            'el-icon-wrench-alt',
            'el-icon-wrench',
            'el-icon-youtube',
            'el-icon-zoom-in',
            'el-icon-zoom-out'
        );
    }
}