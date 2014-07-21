<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Text extends Field
{
    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     */
    public function render()
    {
        if (!empty( $this->field['data'] ) && empty( $this->field['options'] )) {
            if (empty( $this->field['args'] )) {
                $this->field['args'] = array();
            }

            $this->field['options'] = $this->builder->get_wordpress_data( $this->field['data'], $this->field['args'] );
            $this->field['class'] .= " hasOptions ";
        }

        if (empty( $this->value ) && !empty( $this->field['data'] ) && !empty( $this->field['options'] )) {
            $this->value = $this->field['options'];
        }

        $qtip_title = isset( $this->field['text_hint']['title'] ) ? 'qtip-title="' . $this->field['text_hint']['title'] . '" ' : '';
        $qtip_text = isset( $this->field['text_hint']['content'] ) ? 'qtip-content="' . $this->field['text_hint']['content'] . '" ' : '';

        $readonly = isset( $this->field['readonly'] ) ? ' readonly="readonly"' : '';

        if (isset( $this->field['options'] ) && !empty( $this->field['options'] )) {
            //$placeholder = ( isset( $this->field['placeholder'] ) &&  is_array( $this->field['placeholder'] ) ) ? ' placeholder="' . esc_attr( $this->field['placeholder'] ) . '" ' : '';
            $placeholder = $this->field['placeholder'];
            foreach ($this->field['options'] as $k => $v) {
                if (!empty( $placeholder )) {
                    $placeholder = ( is_array(
                            $this->field['placeholder']
                        ) && isset( $this->field['placeholder'][$k] ) ) ? ' placeholder="' . esc_attr(
                            $this->field['placeholder'][$k]
                        ) . '" ' : '';
                }

                echo '<div class="input_wrapper">';
                echo '<label for="' . $this->field['id'] . '-text-' . $k . '">' . $v . '</label> ';
                echo '<input ' . $qtip_title . $qtip_text . 'type="text" id="' . $this->field['id'] . '-text-' . $k . '" name="' . $this->field['name'] . '[' . $k . ']' . $this->field['name_suffix'] . '" ' . $placeholder . 'value="' . esc_attr(
                        $this->value[$k]
                    ) . '" class="regular-text ' . $this->field['class'] . '"' . $readonly . ' /><br />';
                echo '</div>';
            }
        } else {
            $placeholder = ( isset( $this->field['placeholder'] ) && !is_array(
                    $this->field['placeholder']
                ) ) ? ' placeholder="' . esc_attr( $this->field['placeholder'] ) . '" ' : '';
            echo '<input ' . $qtip_title . $qtip_text . 'type="text" id="' . $this->field['id'] . '-text" name="' . $this->field['name'] . $this->field['name_suffix'] . '" ' . $placeholder . 'value="' . esc_attr(
                    $this->value
                ) . '" class="regular-text ' . $this->field['class'] . '"' . $readonly . ' />';
        }
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     */
    public function enqueue()
    {
        wp_enqueue_style(
            'redux-field-text-css',
            \Mozart::parameter( 'wp.plugin.uri' ) . '/mozart/public/bundles/mozart/option/fields/text/field_text.css',
            time(),
            true
        );
    }

}
