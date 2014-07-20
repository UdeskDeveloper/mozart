<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Group extends Field
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     * @return      void
     */
    function __construct( $field = array(), $value = '', $parent )
    {
        //parent::__construct( $parent->sections, $parent->args );
        $this->parent = $parent;
        $this->field = $field;
        $this->value = $value;

    }

    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @return      void
     */
    public function render()
    {
        print_r( $this->value );

        if (isset( $this->field['subfields'] ) && empty( $this->field['fields'] )) {
            $this->field['fields'] = $this->field['subfields'];
            unset( $this->field['subfields'] );
        }

        $groups = $this->value;
        if (!isset( $this->field['groupname'] )) {
            $this->field['groupname'] = "";
        }

        echo '<div id="redux-groups-accordion">';

        // Create dummy content for the adding new ones
        echo '<div class="redux-groups-accordion-group redux-dummy" style="display:none" id="redux-dummy-' . $this->field['id'] . '"><h3><span class="redux-groups-header">' . __(
                "New ",
                "mozart-options"
            ) . $this->field['groupname'] . '</span></h3>';
        echo '<div>'; //according content open

        echo '<table style="margin-top: 0;" class="redux-groups-accordion redux-group form-table no-border">';

        //echo '<input type="hidden" class="slide-sort" data-name="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][@][slide_sort]" id="' . $this->field['id'] . '-slide_sort" value="" />';
        //$field_is_title = true;
        foreach ($this->field['fields'] as $key => $field) {
            $field['name'] = $this->parent->args['opt_name'] . '[' . $field['id'] . ']';
            echo '<tr valign="top">';
            $th = "";
            if (isset( $field['title'] ) && isset( $field['type'] ) && $field['type'] !== "info" && $field['type'] !== "group" && $field['type'] !== "section") {
                $default_mark = ( isset( $field['default'] ) && !empty( $field['default'] ) && isset( $this->parent->options[$field['id']] ) && $this->parent->options[$field['id']] == $field['default'] && !empty( $this->parent->args['default_mark'] ) && isset( $field['default'] ) ) ? $this->parent->args['default_mark'] : '';
                if (!empty( $field['title'] )) {
                    $th = $field['title'] . $default_mark . "";
                }

                if (isset( $field['subtitle'] )) {
                    $th .= '<span class="description">' . $field['subtitle'] . '</span>';
                }
            }
            // TITLE
            // Show if various
            //
            $th .= $this->parent->get_default_output_string( $field ); // Get the default output string if set

            echo '<th scope="row"><div class="redux_field_th">' . $th . '</div></th>';
            echo '<td>';

            if (!isset( $field['class'] )) {
                $field['class'] = "";
            }

            $field['name_suffix'] = "[]";
            if (isset( $this->value[$key] )) {
                $value = $this->value[$key];
            } else {
                $value = "";
            }
            $this->parent->_field_input( $field, $value );
            echo '</td></tr>';
        }
        echo '</table>';
        echo '<a href="javascript:void(0);" class="button deletion redux-groups-remove">' . __(
                'Delete',
                'mozart-options'
            ) . ' ' . $this->field['groupname'] . '</a>';
        echo '</div></div>';


        echo '</div><a href="javascript:void(0);" class="button redux-groups-add button-primary" rel-id="' . $this->field['id'] . '-ul" rel-name="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][slide_title][]">' . __(
                'Add',
                'mozart-options'
            ) . ' ' . $this->field['groupname'] . '</a><br/>';

        //echo '</div>';

    }

    function support_multi( $content, $field, $sort )
    {
        //convert name
        $name = $this->parent->args['opt_name'] . '[' . $field['id'] . ']';
        $content = str_replace( $name, $name . '[' . $sort . ']', $content );
        //we should add $sort to id to fix problem with select field
        $content = str_replace(
            ' id="' . $field['id'] . '-select"',
            ' id="' . $field['id'] . '-select-' . $sort . '"',
            $content
        );

        return $content;
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     * @return        void
     */
    public function enqueue()
    {
        wp_enqueue_script(
            'redux-field-group-js',
            \Mozart::parameter( 'wp.plugin.uri' ) . '/mozart/public/bundles/mozart/option/fields/group/field_group.js',
            array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion', 'wp-color-picker' ),
            time(),
            true
        );

        wp_enqueue_style(
            'redux-field-group-css',
            \Mozart::parameter( 'wp.plugin.uri' ) . '/mozart/public/bundles/mozart/option/fields/group/field_group.css',
            time(),
            true
        );
    }

    public function enqueue_dependencies( $field_type )
    {
        $field_class = 'ReduxFramework_' . $field_type;

        if (!class_exists( $field_class )) {
            $class_file = apply_filters(
                'redux-typeclass-load',
                \Mozart::parameter(
                    'wp.plugin.dir'
                ) . '/mozart/public/bundles/mozart/option/fields/' . $field_type . '/field_' . $field_type . '.php',
                $field_class
            );

            if ($class_file) {
                /** @noinspection PhpIncludeInspection */
                require_once( $class_file );
            }
        }

        if (class_exists( $field_class ) && method_exists( $field_class, 'enqueue' )) {
            $enqueue = new $field_class( '', '', $this );
            $enqueue->enqueue();
        }
    }

}