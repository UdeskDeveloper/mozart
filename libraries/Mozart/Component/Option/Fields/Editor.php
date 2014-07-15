<?php
namespace Mozart\Component\Option\Fields;

use Mozart\Component\Option\Field;

class Editor extends Field
{
    function __construct( $field = array(), $value = '', $parent )
    {
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
        if (!isset( $this->field['args'] )) {
            $this->field['args'] = array();
        }

        $this->field['args']['onchange_callback'] = "alert('here')";

        // Setup up default args
        $defaults = array(
            'textarea_name' => $this->field['name'],
            'editor_class'  => $this->field['class'],
            'textarea_rows' => 10, //Wordpress default
            'teeny'         => true,
        );

        if (isset( $this->field['editor_options'] ) && empty( $this->field['args'] )) {
            $this->field['args'] = $this->field['editor_options'];
            unset( $this->field['editor_options'] );
        }

        $this->field['args'] = wp_parse_args( $this->field['args'], $defaults );

        wp_editor( $this->value, $this->field['id'], $this->field['args'] );
    }


    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     * @return      void
     */
    public function enqueue()
    {
        wp_enqueue_style(
            'redux-field-editor-css',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/editor/field_editor.css',
            time(),
            true
        );

        wp_enqueue_script(
            'redux-field-editor2-js',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/editor/field_editor.js',
            array( 'jquery', 'redux-js' ),
            time(),
            true
        );
    }

}