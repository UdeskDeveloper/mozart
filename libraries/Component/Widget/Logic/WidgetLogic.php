<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Widget\Logic;

/**
 * Class WidgetLogic
 * @package Mozart\Component\Widget\Logic
 */
class WidgetLogic
{
    private $options;
    private $loadPoints;

    public function __construct()
    {

        $this->loadPoints = array(
            'plugins_loaded' => __( 'when plugin starts (default)', 'mozart-widget-logic' ),
            'after_setup_theme' => __( 'after theme loads', 'mozart-widget-logic' ),
            'wp_loaded' => __( 'when all PHP loaded', 'mozart-widget-logic' ),
            'wp_head' => __( 'during page header', 'mozart-widget-logic' )
        );

        if (( !$this->options = get_option( 'widget_logic' ) ) || !is_array( $this->options )) {
            $this->options = array();
        }
    }

    public function initialize()
    {
        load_plugin_textdomain(
            'mozart-widget-logic',
            false,
            WP_PLUGIN_DIR . '/mozart/translations/widget/logic'
        );

        if (is_admin()) {
            // widget changes submitted by ajax method
            add_filter(
                'widget_update_callback',
                array( $this, 'ajax_update_callback' ),
                10,
                3
            );
            // before any HTML output save widget changes and add controls to each widget on the widget admin page
            add_action(
                'sidebar_admin_setup',
                array( $this, 'expand_control' )
            );
            // add Widget Logic specific options on the widget admin page
            add_action(
                'sidebar_admin_page',
                array( $this, 'options_control' )
            );
        } else {
            if (isset( $this->options['options-load_point'] ) &&
                ( $this->options['options-load_point'] != 'plugins_loaded' ) &&
                array_key_exists( $this->options['options-load_point'], $this->loadPoints )
            ) {
                add_action(
                    $this->options['options-load_point'],
                    array( $this, 'sidebars_widgets_filter_add' )
                );
            } else {
                $this->sidebars_widgets_filter_add();
            }

            if (isset( $this->options['options-filter'] ) && $this->options['options-filter'] == 'checked') {
                add_filter( 'dynamic_sidebar_params', array( $this, 'widget_display_callback' ), 10 );
            } // redirect the widget callback so the output can be buffered and filtered
        }
    }

    /**
     * actually remove the widgets from the front end depending on widget logic provided
     */
    public function sidebars_widgets_filter_add()
    {
        add_filter(
            'sidebars_widgets',
            array( $this, 'filter_sidebars_widgets' ),
            10
        );
    }

    /**
     * @param $instance
     * @param $new_instance
     * @param $this_widget
     * @return mixed
     */
    public function ajax_update_callback($instance, $new_instance, $this_widget)
    {
        $widget_id = $this_widget->id;
        if (isset( $_POST[$widget_id . '-widget_logic'] )) {
            $this->options[$widget_id] = trim( $_POST[$widget_id . '-widget_logic'] );
            update_option( 'widget_logic', $this->options );
        }

        return $instance;
    }

    /**
     * adds in the admin control per widget, but also processes import/export
     */
    public function expand_control()
    {
        global $wp_registered_widgets, $wp_registered_widget_controls;

        // EXPORT ALL OPTIONS
        if (isset( $_GET['wl-options-export'] )) {
            header( "Content-Disposition: attachment; filename=options.txt" );
            header( 'Content-Type: text/plain; charset=utf-8' );

            echo "[START=WIDGET LOGIC OPTIONS]\n";
            foreach ($this->options as $id => $text) {
                echo "$id\t" . json_encode( $text ) . "\n";
            }
            echo "[STOP=WIDGET LOGIC OPTIONS]";
            exit;
        }

        // IMPORT ALL OPTIONS
        if (isset( $_POST['wl-options-import'] )) {
            if ($_FILES['wl-options-import-file']['tmp_name']) {
                $import = preg_split( "\n", file_get_contents( $_FILES['wl-options-import-file']['tmp_name'], false ) );
                if (array_shift( $import ) == "[START=WIDGET LOGIC OPTIONS]" && array_pop(
                        $import
                    ) == "[STOP=WIDGET LOGIC OPTIONS]"
                ) {
                    foreach ($import as $import_option) {
                        list( $key, $value ) = preg_split( "\t", $import_option );
                        $this->options[$key] = json_decode( $value );
                    }
                    $this->options['msg'] = __( 'Success! Options file imported', 'mozart-widget-logic' );
                } else {
                    $this->options['msg'] = __( 'Invalid options file', 'mozart-widget-logic' );
                }

            } else {
                $this->options['msg'] = __( 'No options file provided', 'mozart-widget-logic' );
            }

            update_option( 'widget_logic', $this->options );
            wp_redirect( admin_url( 'widgets.php' ) );
            exit;
        }

        // ADD EXTRA WIDGET LOGIC FIELD TO EACH WIDGET CONTROL
        // pop the widget id on the params array (as it's not in the main params so not provided to the callback)
        foreach ($wp_registered_widgets as $id => $widget) { // controll-less widgets need an empty function so the callback function is called.
            if (!isset( $wp_registered_widget_controls[$id] )) {
                wp_register_widget_control( $id, $widget['name'], 'empty_control' );
            }
            $wp_registered_widget_controls[$id]['callback_wl_redirect'] = $wp_registered_widget_controls[$id]['callback'];
            $wp_registered_widget_controls[$id]['callback'] = array( $this, 'extra_control' );
            array_push( $wp_registered_widget_controls[$id]['params'], $id );
        }


        // UPDATE WIDGET LOGIC WIDGET OPTIONS (via accessibility mode?)
        if ('post' == strtolower( $_SERVER['REQUEST_METHOD'] )) {
            foreach ((array) $_POST['widget-id'] as $widget_number => $widget_id) {
                if (isset( $_POST[$widget_id . '-widget_logic'] )) {
                    $this->options[$widget_id] = trim( $_POST[$widget_id . '-widget_logic'] );
                }
            }

            // clean up empty options (in PHP5 use array_intersect_key)
            $regd_plus_new = array_merge(
                array_keys( $wp_registered_widgets ),
                array_values( (array) $_POST['widget-id'] ),
                array(
                    'options-filter',
                    'options-wp_reset_query',
                    'options-load_point'
                )
            );
            foreach (array_keys( $this->options ) as $key) {
                if (!in_array( $key, $regd_plus_new )) {
                    unset( $this->options[$key] );
                }
            }
        }

        // UPDATE OTHER WIDGET LOGIC OPTIONS
        // must update this to use http://codex.wordpress.org/Settings_API
        if (isset( $_POST['options-submit'] )) {
            $this->options['options-filter'] = $_POST['options-filter'];
            $this->options['options-wp_reset_query'] = $_POST['options-wp_reset_query'];
            $this->options['options-load_point'] = $_POST['options-load_point'];
        }


        update_option( 'widget_logic', $this->options );

    }

    /**
     * output extra HTML
     */
    public function options_control()
    {
        if (isset( $this->options['msg'] )) {
            if (substr( $this->options['msg'], 0, 2 ) == "OK") {
                echo '<div id="message" class="updated">';
            } else {
                echo '<div id="message" class="error">';
            }
            echo '<p>Widget Logic – ' . $this->options['msg'] . '</p></div>';
            unset( $this->options['msg'] );
            update_option( 'widget_logic', $this->options );
        }

        ?>
        <div class="wrap">

            <h2><?php _e( 'Widget Logic options', 'mozart-widget-logic' ); ?></h2>

            <form method="POST" style="float:left; width:45%">
                <ul>
                    <li><label for="options-filter" title="<?php _e(
                            'Adds a new WP filter you can use in your own code. Not needed for main Widget Logic functionality.',
                            'mozart-widget-logic'
                        ); ?>">
                            <input id="options-filter" name="options-filter" type="checkbox"
                                   value="checked"
                                   class="checkbox" <?php if (isset( $this->options['options-filter'] )) {
                                echo "checked";
                            } ?>/>
                            <?php _e( 'Add \'widget_content\' filter', 'mozart-widget-logic' ); ?>
                        </label>
                    </li>
                    <li><label for="options-wp_reset_query" title="<?php _e(
                            'Resets a theme\'s custom queries before your Widget Logic is checked',
                            'mozart-widget-logic'
                        ); ?>">
                            <input id="options-wp_reset_query" name="options-wp_reset_query"
                                   type="checkbox" value="checked"
                                   class="checkbox" <?php if (isset( $this->options['options-wp_reset_query'] )) {
                                echo "checked";
                            } ?> />
                            <?php _e( 'Use \'wp_reset_query\' fix', 'mozart-widget-logic' ); ?>
                        </label>
                    </li>
                    <li><label for="options-load_point" title="<?php _e(
                            'Delays widget logic code being evaluated til various points in the WP loading process',
                            'mozart-widget-logic'
                        ); ?>"><?php _e( 'Load logic', 'mozart-widget-logic' ); ?>
                            <select id="options-load_point" name="options-load_point"><?php
                                foreach ($this->loadPoints as $action => $action_desc) {
                                    echo "<option value='" . $action . "'";
                                    if (isset( $this->options['options-load_point'] ) && $action == $this->options['options-load_point']) {
                                        echo " selected ";
                                    }
                                    echo ">" . $action_desc . "</option>"; //
                                }
                                ?>
                            </select>
                        </label>
                    </li>
                </ul>
                <?php submit_button(
                    __( 'Save WL options', 'mozart-widget-logic' ),
                    'button-primary',
                    'options-submit',
                    false
                ); ?>

            </form>
            <form method="POST" enctype="multipart/form-data" style="float:left; width:45%">
                <a class="submit button" href="?wl-options-export"
                   title="<?php _e(
                       'Save all WL options to a plain text config file',
                       'mozart-widget-logic'
                   ); ?>"><?php _e(
                        'Export options',
                        'mozart-widget-logic'
                    ); ?></a>

                <p>
                    <?php submit_button(
                        __( 'Import options', 'mozart-widget-logic' ),
                        'button',
                        'wl-options-import',
                        false,
                        array(
                            'title' => __(
                                'Load all WL options from a plain text config file',
                                'mozart-widget-logic'
                            )
                        )
                    ); ?>
                    <input type="file" name="wl-options-import-file" id="wl-options-import-file"
                           title="<?php _e( 'Select file for importing', 'mozart-widget-logic' ); ?>"/></p>
            </form>

        </div>

    <?php
    }

    /**
     * added to widget functionality in 'expand_control' (above)
     */
    public function empty_control()
    {
    }

    /**
     * added to widget functionality in 'expand_control' (above)
     */
    public function extra_control()
    {
        global $wp_registered_widget_controls;

        $params = func_get_args();
        $id = array_pop( $params );

        // go to the original control function
        $callback = $wp_registered_widget_controls[$id]['callback_wl_redirect'];
        if (is_callable( $callback )) {
            call_user_func_array( $callback, $params );
        }

        $value = !empty( $this->options[$id] ) ? htmlspecialchars(
            stripslashes( $this->options[$id] ),
            ENT_QUOTES
        ) : '';

        // dealing with multiple widgets - get the number. if -1 this is the 'template' for the admin interface
        $id_disp = $id;
        if (!empty( $params ) && isset( $params[0]['number'] )) {
            $number = $params[0]['number'];
            if ($number == -1) {
                $number = "__i__";
                $value = "";
            }
            $id_disp = $wp_registered_widget_controls[$id]['id_base'] . '-' . $number;
        }

        // output our extra widget logic field
        echo "<p><label for='" . $id_disp . "-widget_logic'>" . __(
                'Widget logic:',
                'mozart-widget-logic'
            ) . " <textarea class='widefat' type='text' name='" . $id_disp . "-widget_logic' id='" . $id_disp . "-widget_logic' >" . $value . "</textarea></label></p>";
    }

    /**
     * @param $sidebars_widgets
     * @return mixed
     */
    public function filter_sidebars_widgets($sidebars_widgets)
    {
        global $wp_reset_query_is_done;

        // reset any database queries done now that we're about to make decisions based on the context given in the WP query for the page
        if (!empty( $this->options['options-wp_reset_query'] ) && ( $this->options['options-wp_reset_query'] == 'checked' ) && empty( $wp_reset_query_is_done )) {
            wp_reset_query();
            $wp_reset_query_is_done = true;
        }

        // loop through every widget in every sidebar (barring 'wp_inactive_widgets') checking WL for each one
        foreach ($sidebars_widgets as $widget_area => $widget_list) {
            if ($widget_area == 'wp_inactive_widgets' || empty( $widget_list )) {
                continue;
            }

            foreach ($widget_list as $pos => $widget_id) {
                if (empty( $this->options[$widget_id] )) {
                    continue;
                }
                $wl_value = stripslashes( trim( $this->options[$widget_id] ) );
                if (empty( $wl_value )) {
                    continue;
                }

                $wl_value = apply_filters( "eval_override", $wl_value );
                if ($wl_value === false) {
                    unset( $sidebars_widgets[$widget_area][$pos] );
                    continue;
                }
                if ($wl_value === true) {
                    continue;
                }

                if (stristr( $wl_value, "return" ) === false) {
                    $wl_value = "return (" . $wl_value . ");";
                }

                if (!eval( $wl_value )) {
                    unset( $sidebars_widgets[$widget_area][$pos] );
                }
            }
        }

        return $sidebars_widgets;
    }

    /**
     * swap out the original call back and replace it with our own
     *
     * @param $params
     * @return mixed
     */
    public function widget_display_callback($params)
    {
        global $wp_registered_widgets;
        $id = $params[0]['widget_id'];
        $wp_registered_widgets[$id]['callback_wl_redirect'] = $wp_registered_widgets[$id]['callback'];
        $wp_registered_widgets[$id]['callback'] = 'redirected_callback';

        return $params;
    }

    public function redirected_callback()
    {
        global $wp_registered_widgets, $wp_reset_query_is_done;

        // replace the original callback data
        $params = func_get_args();
        $id = $params[0]['widget_id'];
        $callback = $wp_registered_widgets[$id]['callback_wl_redirect'];
        $wp_registered_widgets[$id]['callback'] = $callback;

        // run the callback but capture and filter the output using PHP output buffering
        if (is_callable( $callback )) {
            ob_start();
            call_user_func_array( $callback, $params );
            $widget_content = ob_get_contents();
            ob_end_clean();
            echo apply_filters( 'widget_content', $widget_content, $id );
        }
    }
}
