<?php
namespace Mozart\Component\Option\Fields;

use Mozart\Component\Option\Field;

class Password extends Field
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
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
     */
    function render()
    {
        if (!empty( $this->field['username'] ) && $this->field['username'] === true) {
            $this->_render_combined_field();
        } else {
            $this->_render_single_field();
        }
    }

    /**
     * This will render a combined User/Password field
     *
     */
    private function _render_combined_field()
    {
        $defaults = array(
            'username' => '',
            'password' => '',
            'placeholder' => array(
                'password' => __( 'Password', 'mozart-options' ),
                'username' => __( 'Username', 'mozart-options' )
            )
        );

        $this->value = wp_parse_args( $this->value, $defaults );

        if (!empty( $this->field['placeholder'] )) {
            if (is_array( $this->field['placeholder'] )) {
                if (!empty( $this->field['placeholder']['password'] )) {
                    $this->value['placeholder']['password'] = $this->field['placeholder']['password'];
                }
                if (!empty( $this->field['placeholder']['username'] )) {
                    $this->value['placeholder']['username'] = $this->field['placeholder']['username'];
                }
            } else {
                $this->value['placeholder']['password'] = $this->field['placeholder'];
            }
        }

        // Username field
        echo '<input type="text" autocomplete="off" placeholder="' . $this->value['placeholder']['username'] . '" id="' . $this->field['id'] . '[username]" name="' . $this->field['name'] . '[username]' . $this->field['name_suffix'] . '" value="' . esc_attr(
                $this->value['username']
            ) . '" class="' . $this->field['class'] . '" />&nbsp;';

        // Password field
        echo '<input type="password" autocomplete="off" placeholder="' . $this->value['placeholder']['password'] . '" id="' . $this->field['id'] . '[password]" name="' . $this->field['name'] . '[password]' . $this->field['name_suffix'] . '" value="' . esc_attr(
                $this->value['password']
            ) . '" class="' . $this->field['class'] . '" />';
    }

    /**
     * This will render a single Password field
     *
     */
    private function _render_single_field()
    {
        echo '<input type="password" id="' . $this->field['id'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '" value="' . esc_attr(
                $this->value
            ) . '" class="' . $this->field['class'] . '" />';
    }
}
