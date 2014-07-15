<?php
namespace Mozart\Component\Option\Fields;

use Mozart\Component\Option\Field;

class MultiText extends Field
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     * @return      void
     */
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
        $this->add_text = ( isset( $this->field['add_text'] ) ) ? $this->field['add_text'] : __(
            'Add More',
            'mozart-options'
        );
        $this->show_empty = ( isset( $this->field['show_empty'] ) ) ? $this->field['show_empty'] : true;

        echo '<ul id="' . $this->field['id'] . '-ul" class="redux-multi-text">';

        if (isset( $this->value ) && is_array( $this->value )) {
            foreach ($this->value as $k => $value) {
                if ($value != '') {
                    echo '<li><input type="text" id="' . $this->field['id'] . '-' . $k . '" name="' . $this->field['name'] . '[]' . $this->field['name_suffix'] . '" value="' . esc_attr(
                            $value
                        ) . '" class="regular-text ' . $this->field['class'] . '" /> <a href="javascript:void(0);" class="deletion redux-multi-text-remove">' . __(
                            'Remove',
                            'mozart-options'
                        ) . '</a></li>';
                }
            }
        } elseif ($this->show_empty == true) {
            echo '<li><input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[]' . $this->field['name_suffix'] . '" value="" class="regular-text ' . $this->field['class'] . '" /> <a href="javascript:void(0);" class="deletion redux-multi-text-remove">' . __(
                    'Remove',
                    'mozart-options'
                ) . '</a></li>';
        }

        echo '<li style="display:none;"><input type="text" id="' . $this->field['id'] . '" name="" value="" class="regular-text" /> <a href="javascript:void(0);" class="deletion redux-multi-text-remove">' . __(
                'Remove',
                'mozart-options'
            ) . '</a></li>';

        echo '</ul>';
        $this->field['add_number'] = ( isset( $this->field['add_number'] ) && is_numeric(
                $this->field['add_number']
            ) ) ? $this->field['add_number'] : 1;
        echo '<a href="javascript:void(0);" class="button button-primary redux-multi-text-add" data-add_number="' . $this->field['add_number'] . '" data-id="' . $this->field['id'] . '-ul" data-name="' . $this->field['name'] . '[]">' . $this->add_text . '</a><br/>';
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     * @return      void
     */
    public function enqueue()
    {
        wp_enqueue_script(
            'redux-field-multi-text-js',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/multi_text/field_multi_text.js',
            array( 'jquery', 'redux-js' ),
            time(),
            true
        );

        wp_enqueue_style(
            'redux-field-multi-text-css',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/multi_text/field_multi_text.css',
            time(),
            true
        );
    }
}