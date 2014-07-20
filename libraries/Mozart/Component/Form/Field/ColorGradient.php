<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class ColorGradient extends Field
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     *
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
     *
     *
     * @return      void
     */
    public function render()
    {
        // No errors please
        $defaults = array(
            'from' => '',
            'to'   => ''
        );

        $this->value = wp_parse_args( $this->value, $defaults );

        echo '<div class="colorGradient"><strong>' . __( 'From ', 'mozart-options' ) . '</strong>&nbsp;';
        echo '<input data-id="' . $this->field['id'] . '" id="' . $this->field['id'] . '-from" name="' . $this->field['name'] . '[from]' . $this->field['name_suffix'] . '" value="' . $this->value['from'] . '" class="redux-color redux-color-init ' . $this->field['class'] . '"  type="text" data-default-color="' . $this->field['default']['from'] . '" />';
        echo '<input type="hidden" class="redux-saved-color" id="' . $this->field['id'] . '-saved-color' . '" value="">';

        if (!isset( $this->field['transparent'] ) || $this->field['transparent'] !== false) {
            $tChecked = "";

            if ($this->value['from'] == "transparent") {
                $tChecked = ' checked="checked"';
            }

            echo '<label for="' . $this->field['id'] . '-from-transparency" class="color-transparency-check"><input type="checkbox" class="checkbox color-transparency ' . $this->field['class'] . '" id="' . $this->field['id'] . '-from-transparency" data-id="' . $this->field['id'] . '-from" value="1"' . $tChecked . '> ' . __(
                    'Transparent',
                    'mozart-options'
                ) . '</label>';
        }
        echo "</div>";
        echo '<div class="colorGradient toLabel"><strong>' . __(
                'To ',
                'mozart-options'
            ) . '</strong>&nbsp;<input data-id="' . $this->field['id'] . '" id="' . $this->field['id'] . '-to" name="' . $this->field['name'] . '[to]' . $this->field['name_suffix'] . '" value="' . $this->value['to'] . '" class="redux-color redux-color-init ' . $this->field['class'] . '"  type="text" data-default-color="' . $this->field['default']['to'] . '" />';

        if (!isset( $this->field['transparent'] ) || $this->field['transparent'] !== false) {
            $tChecked = "";

            if ($this->value['to'] == "transparent") {
                $tChecked = ' checked="checked"';
            }

            echo '<label for="' . $this->field['id'] . '-to-transparency" class="color-transparency-check"><input type="checkbox" class="checkbox color-transparency" id="' . $this->field['id'] . '-to-transparency" data-id="' . $this->field['id'] . '-to" value="1"' . $tChecked . '> ' . __(
                    'Transparent',
                    'mozart-options'
                ) . '</label>';
        }
        echo "</div>";
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     *
     *
     * @return      void
     */
    public function enqueue()
    {
        wp_enqueue_script(
            'redux-field-color-gradient-js',
            \Mozart::parameter('wp.plugin.uri') . '/mozart/public/bundles/mozart/option/fields/color_gradient/field_color_gradient.js',
            array( 'jquery', 'wp-color-picker', 'redux-js' ),
            time(),
            true
        );

        wp_enqueue_style(
            'redux-field-color_gradient-css',
            \Mozart::parameter('wp.plugin.uri') . '/mozart/public/bundles/mozart/option/fields/color_gradient/field_color_gradient.css',
            time(),
            true
        );
    }
}