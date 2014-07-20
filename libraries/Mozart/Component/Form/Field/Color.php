<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Color extends Field
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     * @since         1.0.0
     * @access        public
     * @return        void
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
     * @since         1.0.0
     * @access        public
     * @return        void
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
     * @since         1.0.0
     * @access        public
     * @return        void
     */
    public function enqueue()
    {
        wp_enqueue_script(
            'redux-field-color-js',
            \Mozart::parameter('wp.plugin.uri') . '/mozart/public/bundles/mozart/option/fields/color/field_color.js',
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
                $css = Redux_Functions::parseCSS( $this->field['output'], $style, $this->value );
                $this->parent->outputCSS .= $css;
            }

            if (!empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] )) {
                $css = Redux_Functions::parseCSS( $this->field['compiler'], $style, $this->value );
                $this->parent->compilerCSS .= $css;

            }
        }
    }
}