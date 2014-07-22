<?php

namespace Mozart\Component\Option\Extension\Core;

use Mozart\Component\Option\OptionBuilder;
use Mozart\Component\Option\Extension\ExtensionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class Customizer
 *
 * @package Mozart\Component\Option\Extensions
 */
class CustomizerExtension implements ExtensionInterface
{
    /**
     * @var array
     */
    protected $options_defaults;
    /**
     * @var array
     */
    private $orig_options;
    /**
     * @var string|void
     */
    private $_extension_url;
    /**
     * @var string
     */
    private $_extension_dir;
    /**
     * @var OptionBuilder
     */
    private $builder;
    /**
     * @var string
     */
    private $currentPage;
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param OptionBuilder $builder
     * @param RequestStack $request
     */
    public function __construct( RequestStack $requestStack )
    {
        $this->currentPage = $GLOBALS['pagenow'];
        $this->requestStack = $requestStack;
    }

    /**
     * Boot the extension
     */
    public function extend( OptionBuilder $builder )
    {
        $this->builder = $builder;

        if ($this->currentPage !== "customize.php"
            && $this->currentPage !== "admin-ajax.php"
            && !isset( $GLOBALS['wp_customize'] )
        ) {
            return false;
        }

        //add_action('wp_head', array( $this, '_enqueue_new' ));

        $this->_extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
        $this->_extension_url = site_url(
            str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->_extension_dir )
        );

        if ('' !== $this->requestStack->get( 'customized' )
            || $this->currentPage === "admin-ajax.php"
        ) {
            if (current_user_can( $this->builder->getParam('page_permissions') )) {
                add_action(
                    'customize_register',
                    array( $this, 'registerCustomizer' )
                );
            }
        }

        if ('' !== $this->requestStack->get( 'customized' )) {
            add_action(
                "redux/options/{$this->builder->getParam('opt_name')}/options",
                array( $this, '_override_values' ),
                100
            );
            add_action( 'customize_save', array( $this, 'customizer_save_before' ) ); // Before save
            add_action( 'customize_save_after', array( &$this, 'customizer_save_after' ) ); // After save
            add_action( 'wp_head', array( $this, 'customize_preview_init' ) );
        }

        //add_action( 'wp_enqueue_scripts', array( &$this, '_enqueue_previewer_css' ) ); // Enqueue previewer css
        //add_action( 'wp_enqueue_scripts', array( &$this, '_enqueue_previewer_js' ) ); // Enqueue previewer javascript
        //add_action( "wp_footer", array( $this, '_enqueue_new' ), 100 );
        //$this->_enqueue_new();
    }

    /**
     *
     */
    public function customize_preview_init()
    {
        do_action( 'redux/customizer/live_preview' );
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function _override_values( $data )
    {
        if (isset( $this->request['customized'] )) {
            $this->orig_options = $this->builder->getOptions();
            $options = json_decode( stripslashes_deep( $this->request['customized'] ), true );

            foreach ($options as $key => $value) {
                if (strpos( $key, $this->builder->getParam('opt_name') ) !== false) {
                    $key = str_replace(
                        $this->builder->getParam('opt_name') . '[',
                        '',
                        rtrim( $key, "]" )
                    );
                    $data[$key] = $value;
                    $GLOBALS[$this->builder->args['global_variable']][$key] = $value;
                    $this->builder->options[$key] = $value;
                }
            }
        }

        return $data;
    }

    /**
     *
     */
    public function _enqueue_new()
    {
        wp_enqueue_script(
            'redux-extension-customizer-codemirror-js',
            $this->_extension_url . 'new/codemirror.min.js'
        );
        wp_enqueue_script( 'redux-extension-customizer-color-js', $this->_extension_url . 'new/colors-control.js' );
        wp_enqueue_script(
            'redux-extension-customizer-controls-js',
            $this->_extension_url . 'new/customizer-control.js'
        );
        wp_enqueue_script(
            'redux-extension-customizer-fonts-js',
            $this->_extension_url . 'new/fonts-customizer-admin.js'
        );
        wp_enqueue_script( 'redux-extension-customizer-header-js', $this->_extension_url . 'new/header-control.js' );
        wp_enqueue_script( 'redux-extension-customizer-models-js', $this->_extension_url . 'new/header-models.js' );
        wp_enqueue_script(
            'redux-extension-customizer-slimscroll-js',
            $this->_extension_url . 'new/jquery.slimscroll.js'
        );
        wp_enqueue_script(
            'redux-extension-customizer-droppable-js',
            $this->_extension_url . 'new/jquery.ui.droppable.min.js'
        );
        wp_enqueue_script( 'redux-extension-customizer-editor-js', $this->_extension_url . 'new/media-editor.min.js' );
        wp_enqueue_script( 'redux-extension-customizer-new-js', $this->_extension_url . 'new/new-customizer.js' );
        wp_enqueue_script( 'redux-extension-customizer-previewing-js', $this->_extension_url . 'new/previewing.js' );
        wp_enqueue_script( 'redux-extension-customizer-theme-js', $this->_extension_url . 'new/theme-customizer.js' );

    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     */
    public function registerCustomizer( \WP_Customize_Manager $wp_customize )
    {
        $order = array(
            'heading' => -500,
            'option'  => -500,
        );

        foreach ($this->builder->getSections() as $section) {

            // Not a type that should go on the customizer
            if (empty( $section['fields'] ) || ( isset( $section['type'] ) && $section['type'] == "divide" )) {
                continue;
            }

            // If section customizer is set to false
            if (isset( $section['customizer'] ) && $section['customizer'] === false) {
                continue;
            }

            // Evaluate section permissions
            if (isset( $section['permissions'] )) {
                if (!current_user_can( $section['permissions'] )) {
                    continue;
                }
            }

            // No errors please
            if (!isset( $section['desc'] )) {
                $section['desc'] = "";
            }

            // Fill the description if there is a subtitle
            if (empty( $section['desc'] ) && !empty( $section['subtitle'] )) {
                $section['desc'] = $section['subtitle'];
            }

            // Let's make a section ID from the title
            if (empty( $section['id'] )) {
                $section['id'] = strtolower( str_replace( " ", "", $section['title'] ) );
            }

            // No title is present, let's show what section is missing a title
            if (!isset( $section['title'] )) {
                print_r( $section );
            }

            // Let's set a default priority
            if (empty( $section['priority'] )) {
                $section['priority'] = $order['heading'];
                $order['heading']++;
            }

            $wp_customize->add_section(
                $section['id'],
                array(
                    'title'       => $section['title'],
                    'priority'    => $section['priority'],
                    'description' => $section['desc']
                )
            );


            foreach ($section['fields'] as $option) {
                // Evaluate section permissions
                if (isset( $option['permissions'] ) && !current_user_can( $option['permissions'] )) {
                    continue;
                }

                if (isset( $option['customizer'] ) && $option['customizer'] === false) {
                    continue;
                }

                if ($this->builder->args['customizer'] === false && ( !isset( $option['customizer'] ) || $option['customizer'] !== true )) {
                    continue;
                }

                //Change the item priority if not set
                if ($option['type'] != 'heading' && !isset( $option['priority'] )) {
                    $option['priority'] = $order['option'];
                    $order['option']++;
                }

                if (!empty( $this->options_defaults[$option['id']] )) {
                    $option['default'] = $this->options_defaults['option']['id'];
                }

                if (!isset( $option['default'] )) {
                    $option['default'] = "";
                }
                if (!isset( $option['title'] )) {
                    $option['title'] = "";
                }

                // Wordpress doesn't support multi-select
                if ($option['type'] == "select" && isset( $option['multi'] ) && $option['multi'] == true) {
                    continue;
                }

                $customSetting = array(
                    'default'        => $option['default'],
                    'type'           => 'option',
                    'capabilities'   => 'edit_theme_options',
                    //'capabilities'   => $this->builder->args['page_permissions'],
                    'transport'      => 'refresh',
                    'theme_supports' => '',
                    //'sanitize_callback' => array( $this, '_field_validation' ),
                    //'sanitize_js_callback' =>array( &$parent, '_field_input' ),
                );

                $option['id'] = $this->builder->getParam('opt_name') . '[' . $option['id'] . ']';

                if ($option['type'] != "heading" || !empty( $option['type'] )) {
                    $wp_customize->add_setting( $option['id'], $customSetting );
                }

                if (!empty( $option['data'] ) && empty( $option['options'] )) {
                    if (empty( $option['args'] )) {
                        $option['args'] = array();
                    }

                    if ($option['data'] == "elusive-icons" || $option['data'] == "elusive-icon" || $option['data'] == "elusive") {
                        $icons_file = \ReduxFramework::$_dir . 'inc/fields/select/elusive-icons.php';
                        $icons_file = apply_filters( 'redux-font-icons-file', $icons_file );

                        if (file_exists( $icons_file )) {
                            require_once $icons_file;
                        }
                    }
                    $option['options'] = $this->builder->get_wordpress_data( $option['data'], $option['args'] );
                }

                switch ($option['type']) {
                    case 'heading':
                        // We don't want to put up the section unless it's used by something visible in the customizer
                        $section = $option;
                        $section['id'] = strtolower( str_replace( " ", "", $option['title'] ) );
                        $order['heading'] = -500;

                        if (!empty( $option['priority'] )) {
                            $section['priority'] = $option['priority'];
                        } else {
                            $section['priority'] = $order['heading'];
                            $order['heading']++;
                        }
                        break;

                    case 'text':
                        if (isset( $option['data'] ) && $option['data']) {
                            continue;
                        }
                        $wp_customize->add_control(
                            $option['id'],
                            array(
                                'label'    => $option['title'],
                                'section'  => $section['id'],
                                'settings' => $option['id'],
                                'priority' => $option['priority'],
                                'type'     => 'text',
                            )
                        );
                        break;

                    case 'select':
                        if (( isset( $option['sortable'] ) && $option['sortable'] )) {
                            continue;
                        }
                        $wp_customize->add_control(
                            $option['id'],
                            array(
                                'label'    => $option['title'],
                                'section'  => $section['id'],
                                'settings' => $option['id'],
                                'priority' => $option['priority'],
                                'type'     => 'select',
                                'choices'  => $option['options']
                            )
                        );
                        break;

                    case 'radio':
                        //continue;
                        $wp_customize->add_control(
                            $option['id'],
                            array(
                                'label'    => $option['title'],
                                'section'  => $section['id'],
                                'settings' => $option['id'],
                                'priority' => $option['priority'],
                                'type'     => 'radio',
                                'choices'  => $option['options']
                            )
                        );
                        break;

                    case 'checkbox':
                        if (( isset( $option['data'] ) && $option['data'] ) || ( ( isset( $option['multi'] ) && $option['multi'] ) ) || ( ( isset( $option['options'] ) && !empty( $option['options'] ) ) )) {
                            continue;
                        }
                        $wp_customize->add_control(
                            $option['id'],
                            array(
                                'label'    => $option['title'],
                                'section'  => $section['id'],
                                'settings' => $option['id'],
                                'priority' => $option['priority'],
                                'type'     => 'checkbox',
                            )
                        );
                        break;

                    case 'media':
                        $wp_customize->add_control(
                            new \WP_Customize_Image_Control(
                                $wp_customize, $option['id'], array(
                                    'label'    => $option['title'],
                                    'section'  => $section['id'],
                                    'settings' => $option['id'],
                                    'priority' => $option['priority']
                                )
                            )
                        );
                        break;

                    case 'color':
                        $wp_customize->add_control(
                            new \WP_Customize_Color_Control(
                                $wp_customize, $option['id'], array(
                                    'label'    => $option['title'],
                                    'section'  => $section['id'],
                                    'settings' => $option['id'],
                                    'priority' => $option['priority']
                                )
                            )
                        );
                        break;

                    case 'switch':
                        continue;
//                                $wp_customize->add_control( new Redux_customizer_switch( $wp_customize, $option['id'], array(
//                                    'label'          => $option['title'],
//                                    'section'        => $section['id'],
//                                    'settings'       => $option['id'],
//                                    'field'          => $option,
//                                    'ReduxFramework' => $this->builder,
//                                    'priority'       => $option['priority'],
//                                ) ) );

                        break;

                    default:
                        break;
                }
            }
        }
    }

    /**
     * @param $plugin_options
     */
    public function customizer_save_before( $plugin_options )
    {
        $this->before_save = $this->builder->options;
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     */
    public function customizer_save_after( \WP_Customize_Manager $wp_customize )
    {
        $options = json_decode( stripslashes_deep( $this->request['customized'] ), true );
        $compiler = false;
        $changed = array();

        foreach ($options as $key => $value) {
            if (strpos( $key, $this->builder->getParam('opt_name') ) !== false) {
                $key = str_replace( $this->builder->getParam('opt_name') . '[', '', rtrim( $key, "]" ) );

                if (false === isset( $this->orig_options[$key] )
                    || $this->orig_options[$key] != $value
                    || (
                        isset( $this->orig_options[$key] )
                        && false === empty( $this->orig_options[$key] )
                        && empty( $value )
                    )
                ) {
                    $changed[$key] = $value;
                    if (isset( $this->builder->compiler_fields[$key] )) {
                        $compiler = true;
                    }
                }
            }
        }

        if (!empty( $changed )) {
            setcookie( "redux-saved-{$this->builder->getParam('opt_name')}", 1, time() + 1000, "/" );
        }

        if ($compiler) {
            // Have to set this to stop the output of the CSS and typography stuff.
            $this->builder->no_output = true;
            $this->builder->_enqueue_output();
            do_action(
                "redux/options/{$this->builder->getParam('opt_name')}/compiler",
                $this->builder->options,
                $this->builder->compilerCSS
            );
        }
    }

    /**
     * Enqueue CSS/JS for preview pane
     *
     *
     *
     * @global      $wp_styles
     * @return void
     */
    public function _enqueue_previewer()
    {
        wp_enqueue_script(
            'redux-extension-previewer-js',
            $this->_extension_url . 'assets/js/preview.js'
        );

        $localize = array(
            'save_pending'   => __(
                'You have changes that are not saved. Would you like to save them now?',
                'redux-framework'
            ),
            'reset_confirm'  => __( 'Are you sure? Resetting will lose all custom values.', 'redux-framework' ),
            'preset_confirm' => __(
                'Your current options will be replaced with the values of this preset. Would you like to proceed?',
                'redux-framework'
            ),
            'opt_name'       => $this->builder->getParam('opt_name'),
            //'folds'             => $this->folds,
            'options'        => $this->builder->options,
            'defaults'       => $this->builder->options_defaults,
        );

        wp_localize_script(
            'redux-extension-previewer-js',
            'reduxPost',
            $localize
        );
    }

    /**
     * Enqueue CSS/JS for the customizer controls
     *
     *
     *
     * @global      $wp_styles
     * @return void
     */
    public function _enqueue()
    {
        // global $wp_styles;

        //wp_enqueue_style( 'wp-pointer' );
        //wp_enqueue_script( 'wp-pointer' );
        // Remove when code is in place!
        //wp_enqueue_script('redux-extension-customizer-js', $this->_extension_url . 'assets/js/customizer.js');
        // Get styles
        //wp_enqueue_style('redux-extension-customizer-css', $this->_extension_url . 'assets/css/customizer.css');

        $localize = array(
            'save_pending'   => __(
                'You have changes that are not saved.  Would you like to save them now?',
                'redux-framework'
            ),
            'reset_confirm'  => __( 'Are you sure?  Resetting will lose all custom values.', 'redux-framework' ),
            'preset_confirm' => __(
                'Your current options will be replaced with the values of this preset.  Would you like to proceed?',
                'redux-framework'
            ),
            'opt_name'       => $this->builder->getParam('opt_name'),
            //'folds'             => $this->folds,
            'field'          => $this->builder->options,
            'defaults'       => $this->builder->options_defaults,
        );

        // Values used by the javascript
        wp_localize_script(
            'redux-js',
            'redux_opts',
            $localize
        );

        do_action( 'redux-enqueue-' . $this->builder->getParam('opt_name') );

        foreach ($this->builder->getSections() as $section) {
            if (!isset( $section['fields'] )) {
                continue;
            }
            foreach ($section['fields'] as $field) {
                if (!isset( $field['type'] )) {
                    continue;
                }

                $fieldClass = "Mozart\\Component\\Form\\Field\\" . ucfirst(Str::camel( $field['type'] ));

                if (false === class_exists( $fieldClass )) {
                    if (false === class_exists( $fieldClass . 'Field' )) {
                        return false;
                    } else {
                        $fieldClass = $fieldClass . 'Field';
                    }
                }
                if (class_exists( $fieldClass ) && method_exists( $fieldClass, 'enqueue' )) {
                    $enqueue = new $fieldClass( '', '', $this );
                    $enqueue->enqueue();
                }
            }
        }
    }

    /**
     * Register Option for use
     *
     * @return void
     */
    public function _register_setting()
    {

    }

    /**
     * Validate the options before insertion
     *
     * @param array $plugin_options The options array
     *
     * @return
     */
    public function _field_validation( $plugin_options, $two )
    {
        echo $two;

        return $plugin_options;
    }

    /**
     * HTML OUTPUT.
     * @return void
     */
    public function _customizer_html_output()
    {

    }
}
