<?php
namespace Mozart\Component\Option\Fields\Color;

use Mozart\Component\Option\Utils;

class RGBA
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function

     * @return        void
     */
    function __construct( $field = array(), $value = array(), $parent )
    {
        $this->parent = $parent;
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings

     * @return        void
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
                $style = $mode . ':rgba(' . Utils\Option::hex2rgba(
                        $this->value['color']
                    ) . ',' . $this->value['alpha'] . ');';
            }

            if (!empty( $this->field['output'] ) && is_array( $this->field['output'] )) {
                $css = Redux_Functions::parseCSS( $this->field['output'], $style, $this->value );
                $this->parent->outputCSS .= $css;
            }

            if (!empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] )) {
                $css = Redux_Functions::parseCSS( $this->field['compiler'], $style, $this->value );
                $this->parent->compilerCSS .= $css;
            }
        }
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css

     * @return      void
     */
    public function enqueue()
    {
        $min = Redux_Functions::isMin();

        wp_enqueue_script(
            'redux-field-color_rgba-minicolors-js',
            ReduxFramework::$_url . 'src/fields/color_rgba/vendor/minicolors/jquery.minicolors' . $min . '.js',
            array( 'jquery' ),
            time(),
            true
        );

        wp_enqueue_script(
            'redux-field-color_rgba-js',
            ReduxFramework::$_url . 'src/fields/color_rgba/field_color_rgba' . $min . '.js',
            array( 'jquery', 'redux-js' ),
            time(),
            true
        );

        wp_enqueue_style(
            'redux-field-color_rgba-css',
            ReduxFramework::$_url . 'src/fields/color_rgba/field_color_rgba.css',
            time(),
            true
        );
    }
}