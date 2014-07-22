<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Date extends Field
{
    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @return void
     */
    public function render()
    {
        $placeholder = ( isset( $this->field['placeholder'] ) ) ? ' placeholder="' . esc_attr(
                $this->field['placeholder']
            ) . '" ' : '';

        echo '<input data-id="' . $this->field['id'] . '" type="text" id="' . $this->field['id'] . '-date" name="' . $this->field['name'] . $this->field['name_suffix'] . '"' . $placeholder . 'value="' . $this->value . '" class="redux-datepicker ' . $this->field['class'] . '" />';
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     * @return void
     */
    public function enqueue()
    {
        wp_enqueue_style(
            'redux-field-date-css',
            \Mozart::parameter( 'wp.plugin.uri' ) . '/mozart/public/bundles/mozart/form/fields/date/field_date.css',
            time(),
            true
        );

        wp_enqueue_script(
            'redux-field-date-js',
            \Mozart::parameter( 'wp.plugin.uri' ) . '/mozart/public/bundles/mozart/form/fields/date/field_date.js',
            array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'redux-js' ),
            time(),
            true
        );
    }
}
