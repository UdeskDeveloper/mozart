<?php

namespace Mozart\Bundle\BackofficeBundle\Redux;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Configuration
 *
 * @package Mozart\Bundle\BackofficeBundle\Redux
 */
class Configuration
{
    /**
     * @var array
     */
    protected $args = array();
    /**
     * @var array
     */
    protected $sections = array();
    /**
     * @var
     */
    protected $theme;

    /**
     *
     */
    public function init( array $configs = array(), ContainerInterface $container )
    {
        if (!isset( $configs['sections'] )) {
            $configs['sections'] = array();
        }
        if (!isset( $configs['args'] )) {
            $configs['args'] = array();
        }
        // Just for demo purposes. Not needed per say.
        $this->theme = wp_get_theme();

        // Set the default arguments
        $this->setArguments( $configs['args'] );

        $configs['sections'] = array_merge(
            $configs['sections'],
            $container->get( 'redux.sectionmanager' )->getSections()
        );


        // Create the sections and fields
        $this->setSections( $configs['sections'] );

        if (!isset( $this->args['opt_name'] )) { // No errors please
            return;
        }

        // Function to test the compiler hook and demo CSS output.
        // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
        //add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 3);

        // Change the arguments after they've been declared, but before the panel is created
        //add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );

        // Change the default value of a field after it's been set, but before it's been useds
        //    add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );

        // Dynamically add a section. Can be also used to modify sections/fields
        //   add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

        new \ReduxFramework( $this->sections, $this->args );
    }

    /**
     *
     * This is a test function that will let you see when the compiler hook occurs.
     * It only runs if a field    set with compiler=>true is changed.
     * */
    function compiler_action( $configs, $css, $changed_values )
    {
        echo '<h1>The compiler hook has run!</h1>';
        echo "<pre>";
        print_r( $changed_values ); // Values that have changed since the last save
        echo "</pre>";
        //print_r($configs); //Option values
        //print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )

        /*
          // Demo of how to use the dynamic CSS and write your own static CSS file
          $filename = dirname(__FILE__) . '/style' . '.css';
          global $wp_filesystem;
          if( empty( $wp_filesystem ) ) {
            require_once( ABSPATH .'/wp-admin/includes/file.php' );
          WP_Filesystem();
          }

          if( $wp_filesystem ) {
            $wp_filesystem->put_contents(
                $filename,
                $css,
                FS_CHMOD_FILE // predefined mode settings for WP files
            );
          }
         */
    }

    /**
     *
     * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
     * Simply include this function in the child themes functions.php file.
     *
     * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
     * so you must use get_template_directory_uri() if you want to use any of the built in icons
     * */
    function dynamic_section( $sections )
    {
        //$sections = array();
        $sections[] = array(
            'title'  => __( 'Section via hook', 'mozart' ),
            'desc'   => __(
                '<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>',
                'mozart'
            ),
            'icon'   => 'el-icon-paper-clip',
            // Leave this as a blank section, no options just some intro text set above.
            'fields' => array()
        );

        return $sections;
    }

    /**
     *
     * Filter hook for filtering the default value of any given field.
     * Very useful in development mode.
     * */
    function change_defaults( $defaults )
    {
        $defaults['str_replace'] = 'Testing filter hook!';

        return $defaults;
    }

    /**
     *
     */
    public function setSections( array $sections = array() )
    {
        $this->sections = array_merge( $sections, include __DIR__ . '/../Resources/config/redux/sections.php' );
    }

    /**
     *
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to:
     * https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */
    public function setArguments( array $args = array() )
    {
        $this->args = array_merge( $args, include __DIR__ . '/../Resources/config/redux/arguments.php' );
    }

}