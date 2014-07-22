<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Checkbox extends Field
{
    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     *
     *
     * @return void
     */
    public function render()
    {
        $this->field['data_class'] = ( isset( $this->field['multi_layout'] ) ) ? 'data-' . $this->field['multi_layout'] : 'data-full';

        if (!empty( $this->field['options'] ) && ( is_array( $this->field['options'] ) || is_array(
                    $this->field['default']
                ) )
        ) {

            echo '<ul class="' . $this->field['data_class'] . '">';

            if (!isset( $this->value )) {
                $this->value = array();
            }

            if (!is_array( $this->value )) {
                $this->value = array();
            }

            if (empty( $this->field['options'] ) && isset( $this->field['default'] ) && is_array(
                    $this->field['default']
                )
            ) {
                $this->field['options'] = $this->field['default'];
            }

            foreach ($this->field['options'] as $k => $v) {

                if (empty( $this->value[$k] )) {
                    $this->value[$k] = "";
                }

                echo '<li>';
                echo '<label for="' . strtr(
                        $this->builder->getParam('opt_name') . '[' . $this->field['id'] . '][' . $k . ']',
                        array(
                            '[' => '_',
                            ']' => ''
                        )
                    ) . '_' . array_search( $k, array_keys( $this->field['options'] ) ) . '">';
                echo '<input type="hidden" class="checkbox-check" data-val="1" name="' . $this->field['name'] . '[' . $k . ']' . $this->field['name_suffix'] . '" value="' . $this->value[$k] . '" ' . '/>';
                echo '<input type="checkbox" class="checkbox ' . $this->field['class'] . '" id="' . strtr(
                        $this->builder->getParam('opt_name') . '[' . $this->field['id'] . '][' . $k . ']',
                        array(
                            '[' => '_',
                            ']' => ''
                        )
                    ) . '_' . array_search( $k, array_keys( $this->field['options'] ) ) . '" value="1" ' . checked(
                        $this->value[$k],
                        '1',
                        false
                    ) . '/>';
                echo ' ' . $v . '</label>';
                echo '</li>';
            }

            echo '</ul>';
        } elseif (empty( $this->field['data'] )) {

            echo ( !empty( $this->field['desc'] ) ) ? ' <ul class="data-full"><li><label for="' . strtr(
                    $this->builder->getParam('opt_name') . '[' . $this->field['id'] . ']',
                    array(
                        '[' => '_',
                        ']' => ''
                    )
                ) . '">' : '';

            // Got the "Checked" status as "0" or "1" then insert it as the "value" option
            //$ch_value = 1; // checked($this->value, '1', false) == "" ? "0" : "1";
            echo '<input type="hidden" class="checkbox-check" data-val="1" name="' . $this->field['name'] . $this->field['name_suffix'] . '" value="' . $this->value . '" ' . '/>';
            echo '<input type="checkbox" id="' . strtr(
                    $this->builder->getParam('opt_name') . '[' . $this->field['id'] . ']',
                    array(
                        '[' => '_',
                        ']' => ''
                    )
                ) . '" value="1" class="checkbox ' . $this->field['class'] . '" ' . checked(
                    $this->value,
                    '1',
                    false
                ) . '/></li></ul>';

        }
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     *
     *
     * @return void
     */
    public function enqueue()
    {
        wp_enqueue_style(
            'redux-field-checkbox-css',
            \Mozart::parameter('wp.plugin.uri') . '/mozart/public/bundles/mozart/option/fields/checkbox/field_checkbox.css',
            time(),
            true
        );

        wp_enqueue_script(
            'redux-field-checkbox-js',
            \Mozart::parameter('wp.plugin.uri') . '/mozart/public/bundles/mozart/option/fields/checkbox/field_checkbox.js',
            array( 'jquery', 'redux-js' ),
            time(),
            true
        );
    }
}
