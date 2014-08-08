<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Spinner extends Field
{
    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     */
    public function render()
    {
        // Don't allow input edit if there's a step
        $readonly = "";
        if (isset( $this->field['edit'] ) && $this->field['edit'] == false) {
            $readonly = ' readonly="readonly"';
        }

        echo '<input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '" value="' . $this->value . '" class="mini spinner-input' . $this->field['class'] . '"' . $readonly . '/>';
        echo '<div id="' . $this->field['id'] . '-spinner" class="redux_spinner" rel="' . $this->field['id'] . '"></div>';
    }

    /**
     * Clean the field data to the fields defaults given the parameters.
     */
    public function clean()
    {
        if (empty( $this->field['min'] )) {
            $this->field['min'] = 0;
        } else {
            $this->field['min'] = intval( $this->field['min'] );
        }

        if (empty( $this->field['max'] )) {
            $this->field['max'] = intval( $this->field['min'] ) + 1;
        } else {
            $this->field['max'] = intval( $this->field['max'] );
        }

        if (empty( $this->field['step'] ) || $this->field['step'] > $this->field['max']) {
            $this->field['step'] = 1;
        } else {
            $this->field['step'] = intval( $this->field['step'] );
        }

        if (empty( $this->value ) && !empty( $this->field['default'] ) && intval( $this->field['min'] ) >= 1) {
            $this->value = intval( $this->field['default'] );
        }

        if (empty( $this->value ) && intval( $this->field['min'] ) >= 1) {
            $this->value = intval( $this->field['min'] );
        }

        if (empty( $this->value )) {
            $this->value = 0;
        }

        // Extra Validation
        if ($this->value < $this->field['min']) {
            $this->value = intval( $this->field['min'] );
        } elseif ($this->value > $this->field['max']) {
            $this->value = intval( $this->field['max'] );
        }
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     *
     */
    public function enqueue()
    {
        wp_enqueue_script(
            'redux-field-spinner-custom-js',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/form/fields/spinner/vendor/spinner_custom.js',
            array( 'jquery' ),
            time(),
            true
        );

        wp_enqueue_script(
            'redux-field-spinner-js',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/form/fields/spinner/field_spinner.js',
            array(
                'jquery',
                'redux-field-spinner-custom-js',
                'jquery-ui-core',
                'jquery-ui-dialog',
                'redux-js'
            ),
            time(),
            true
        );

        wp_enqueue_style(
            'redux-field-spinner-css',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/form/fields/spinner/field_spinner.css',
            time(),
            true
        );
    }

    /**
     * Functions to pass data from the PHP to the JS at render time.
     *
     * @return array Params to be saved as a javascript object accessable to the UI.
     */
    public function localize($field, $value = "")
    {
        $params = array(
            'id'      => '',
            'min'     => '',
            'max'     => '',
            'step'    => '',
            'val'     => '',
            'default' => '',
        );

        $params = wp_parse_args( $field, $params );

        if (empty( $value )) {
            $value = $this->value;
        }

        $params['val'] = $value;

        return $params;
    }
}
