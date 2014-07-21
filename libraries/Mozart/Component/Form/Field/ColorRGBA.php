<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Option\Utils;
use Mozart\Component\Form\Field;

class ColorRGBA extends Field
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function

     * @return void
     */
    public function __construct( $field = array(), $value = array(), $parent )
    {
        $this->parent = $parent;
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings

     * @return void
     */
    public function render()
    {
        $defaults = array(
            'color' => '',
            'alpha' => '',
        );

        $this->value = wp_parse_args( $this->value, $defaults );

        echo '<input data-id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[color]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-color" class="redux-color_rgba redux-color_rgba-init ' . $this->field['class'] . '"  type="text" value="' . $this->value['color'] . '"  data-default-color="' . $this->field['default']['color'] . '" data-defaultvalue="' . $this->field['default']['color'] . '" data-opacity="' . $this->value['alpha'] . '" />';
        echo '<input data-id="' . $this->field['id'] . '-alpha" name="' . $this->field['name'] . '[alpha]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-alpha" type="hidden" value="' . $this->value['alpha'] . '" />';

        if (!isset( $this->field['transparent'] ) || $this->field['transparent'] !== false) {
            $tChecked = "";

            if ($this->value['alpha'] == "0.00") {
                $tChecked = ' checked="checked"';
            }

            echo '<label for="' . $this->field['id'] . '-transparency" class="color_rgba-transparency-check"><input type="checkbox" class="checkbox color_rgba-transparency ' . $this->field['class'] . '" id="' . $this->field['id'] . '-transparency" data-id="' . $this->field['id'] . '-color" value="1"' . $tChecked . '> ' . __(
                    'Transparent',
                    'mozart-options'
                ) . '</label>';
        }
    }

    public function output()
    {
        if (( !isset( $this->field['output'] ) || !is_array(
                    $this->field['output']
                ) ) && ( !isset( $this->field['compiler'] ) )
        ) {
            return;
        }

        if (!empty( $this->value )) {
            $mode = ( isset( $this->field['mode'] ) && !empty( $this->field['mode'] ) ? $this->field['mode'] : 'color' );

            if ($this->value['alpha'] == "0.00" || empty( $this->value['color'] )) {
                $style = $mode . ':transparent;';
            } elseif (!empty( $this->value['color'] )) {
                $style = $mode . ':rgba(' . $this->hex2rgba(
                        $this->value['color']
                    ) . ',' . $this->value['alpha'] . ');';
            }

            if (!empty( $this->field['output'] ) && is_array( $this->field['output'] )) {
                $css = $this->parseCSS( $this->field['output'], $style, $this->value );
                $this->parent->outputCSS .= $css;
            }

            if (!empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] )) {
                $css = $this->parseCSS( $this->field['compiler'], $style, $this->value );
                $this->parent->compilerCSS .= $css;
            }
        }
    }


    /**
     * Field Render Function.
     * Takes the color hex value and converts to a rgba.
     */
    private function hex2rgba($hex, $alpha = '')
    {
        $hex = str_replace( "#", "", $hex );
        if (strlen( $hex ) == 3) {
            $r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
            $g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
            $b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
        } else {
            $r = hexdec( substr( $hex, 0, 2 ) );
            $g = hexdec( substr( $hex, 2, 2 ) );
            $b = hexdec( substr( $hex, 4, 2 ) );
        }
        $rgb = $r . ',' . $g . ',' . $b;

        if ('' == $alpha) {
            return $rgb;
        } else {
            $alpha = floatval( $alpha );

            return 'rgba(' . $rgb . ',' . $alpha . ')';
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

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css

     * @return void
     */
    public function enqueue()
    {
        wp_enqueue_script(
            'redux-field-color_rgba-minicolors-js',
            \Mozart::parameter('wp.plugin.uri') . '/mozart/public/bundles/mozart/option/fields/color_rgba/vendor/minicolors/jquery.minicolors.js',
            array( 'jquery' ),
            time(),
            true
        );

        wp_enqueue_script(
            'redux-field-color_rgba-js',
            \Mozart::parameter('wp.plugin.uri') . '/mozart/public/bundles/mozart/option/fields/color_rgba/field_color_rgba.js',
            array( 'jquery', 'redux-js' ),
            time(),
            true
        );

        wp_enqueue_style(
            'redux-field-color_rgba-css',
            \Mozart::parameter('wp.plugin.uri') . '/mozart/public/bundles/mozart/option/fields/color_rgba/field_color_rgba.css',
            time(),
            true
        );
    }
}
