<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Color extends Field
{
    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @return void
     */
    public function render()
    {
        echo '<input data-id="' . $this->field['id'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-color" class="redux-color redux-color-init ' . $this->field['class'] . '"  type="text" value="' . $this->value . '" data-oldcolor=""  data-default-color="' . ( isset( $this->field['default'] ) ? $this->field['default'] : "" ) . '" />';
        echo '<input type="hidden" class="redux-saved-color" id="' . $this->field['id'] . '-saved-color' . '" value="">';

        if (!isset( $this->field['transparent'] ) || $this->field['transparent'] !== false) {

            $tChecked = "";

            if ($this->value == "transparent") {
                $tChecked = ' checked="checked"';
            }

            echo '<label for="' . $this->field['id'] . '-transparency" class="color-transparency-check"><input type="checkbox" class="checkbox color-transparency ' . $this->field['class'] . '" id="' . $this->field['id'] . '-transparency" data-id="' . $this->field['id'] . '-color" value="1"' . $tChecked . '> ' . __(
                    'Transparent',
                    'mozart-options'
                ) . '</label>';
        }
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     * @return void
     */
    public function enqueue()
    {
        wp_enqueue_script(
            'redux-field-color-js',
            \Mozart::parameter('wp.plugin.uri') . '/mozart/public/bundles/mozart/form/fields/color/field_color.js',
            array( 'jquery', 'wp-color-picker', 'redux-js' ),
            time(),
            true
        );
    }

    public function output()
    {
        $style = '';

        if (!empty( $this->value )) {
            $mode = ( isset( $this->field['mode'] ) && !empty( $this->field['mode'] ) ? $this->field['mode'] : 'color' );

            $style .= $mode . ':' . $this->value . ';';

            if (!empty( $this->field['output'] ) && is_array( $this->field['output'] )) {
                $css = $this->parseCSS( $this->field['output'], $style, $this->value );
                $this->builder->outputCSS .= $css;
            }

            if (!empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] )) {
                $css = $this->parseCSS( $this->field['compiler'], $style, $this->value );
                $this->builder->compilerCSS .= $css;

            }
        }
    }

    /**
     * Parse CSS from output/compiler array
     *
     * @return $css CSS string
     */
    private function parseCSS( $cssArray = array(), $style = '', $value = '' )
    {
        $css = '';

        if (count( $cssArray ) == 0) {
            return $css;
        } else {

            $keys = implode( ",", $cssArray );

            foreach ($cssArray as $element => $selector) {

                // The old way
                if ($element === 0) {
                    return $keys . "{" . $style . '}';
                }

                // New way continued
                $cssStyle = $element . ':' . $value . ';';

                $css .= $selector . '{' . $cssStyle . '}';
            }
        }

        return $css;
    }
}
