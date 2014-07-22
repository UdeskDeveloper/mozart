<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class SwitchField extends Field
{
    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     */
    public function render()
    {
        $cb_enabled = $cb_disabled = ''; //no errors, please
        //
        //Get selected
        if ((int) $this->value == 1) {
            $cb_enabled = ' selected';
        } else {
            $cb_disabled = ' selected';
        }

        //Label ON
        if (!isset( $this->field['on'] )) {
            $on = __( 'On', 'mozart-options' );
        } else {
            $on = $this->field['on'];
        }

        //Label OFF
        if (!isset( $this->field['off'] )) {
            $off = __( 'Off', 'mozart-options' );
        } else {
            $off = $this->field['off'];
        }

        echo '<div class="switch-options">';
        echo '<label class="cb-enable' . $cb_enabled . '" data-id="' . $this->field['id'] . '"><span>' . $on . '</span></label>';
        echo '<label class="cb-disable' . $cb_disabled . '" data-id="' . $this->field['id'] . '"><span>' . $off . '</span></label>';
        //echo '<input type="hidden" class="checkbox checkbox-input' . $this->field['class'] . '" id="' . $this->field['id'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '" value="' . $this->value . '" />';
        echo '<input type="hidden" class="checkbox checkbox-input' . $this->field['class'] . '" id="' . $this->field['id'] . '" name="' . $this->builder->getParam('opt_name') . '[' . $this->field['id'] . ']' . $this->field['name_suffix'] . '" value="' . $this->value . '" />';
        echo '</div>';
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     */
    public function enqueue()
    {
        wp_enqueue_script(
            'redux-field-switch-js',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/switch/field_switch.js',
            array( 'jquery', 'redux-js' ),
            time(),
            true
        );

        wp_enqueue_style(
            'redux-field-switch-css',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/switch/field_switch.css',
            time(),
            true
        );
    }
}
