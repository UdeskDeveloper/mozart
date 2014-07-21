<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Radio extends Field
{
    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     */
    public function render()
    {
        if (!empty( $this->field['data'] ) && empty( $this->field['options'] )) {
            if (empty( $this->field['args'] )) {
                $this->field['args'] = array();
            }
            $this->field['options'] = $this->builder->get_wordpress_data( $this->field['data'], $this->field['args'] );
        }

        $this->field['data_class'] = ( isset( $this->field['multi_layout'] ) ) ? 'data-' . $this->field['multi_layout'] : 'data-full';

        if (!empty( $this->field['options'] )) {
            echo '<ul class="' . $this->field['data_class'] . '">';

            foreach ($this->field['options'] as $k => $v) {
                echo '<li>';
                echo '<label for="' . $this->field['id'] . '_' . array_search(
                        $k,
                        array_keys( $this->field['options'] )
                    ) . '">';
                echo '<input type="radio" class="radio ' . $this->field['class'] . '" id="' . $this->field['id'] . '_' . array_search(
                        $k,
                        array_keys( $this->field['options'] )
                    ) . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '" value="' . $k . '" ' . checked(
                        $this->value,
                        $k,
                        false
                    ) . '/>';
                echo ' <span>' . $v . '</span>';
                echo '</label>';
                echo '</li>';
            }
            //foreach

            echo '</ul>';
        }
    }
}
