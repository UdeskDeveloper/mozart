<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class LinkColor extends Field
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

        $defaults = array(
            'regular' => true,
            'hover'   => true,
            'visited' => false,
            'active'  => true
        );
        $this->field = wp_parse_args( $this->field, $defaults );

        $defaults = array(
            'regular' => '',
            'hover'   => '',
            'visited' => '',
            'active'  => ''
        );

        $this->value = wp_parse_args( $this->value, $defaults );

        // In case user passes no default values.
        if (isset( $this->field['default'] )) {
            $this->field['default'] = wp_parse_args( $this->field['default'], $defaults );
        } else {
            $this->field['default'] = $defaults;
        }
    }

    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @since       1.0.0
     * @access      public
     * @return      void
     */
    public function render()
    {
        if ($this->field['regular'] === true && $this->field['default']['regular'] !== false) {
            echo '<span class="linkColor"><strong>' . __(
                    'Regular',
                    'mozart-options'
                ) . '</strong>&nbsp;<input id="' . $this->field['id'] . '-regular" name="' . $this->field['name'] . '[regular]' . $this->field['name_suffix'] . '" value="' . $this->value['regular'] . '" class="redux-color redux-color-regular redux-color-init ' . $this->field['class'] . '"  type="text" data-default-color="' . $this->field['default']['regular'] . '" /></span>';
        }

        if ($this->field['hover'] === true && $this->field['default']['hover'] !== false) {
            echo '<span class="linkColor"><strong>' . __(
                    'Hover',
                    'mozart-options'
                ) . '</strong>&nbsp;<input id="' . $this->field['id'] . '-hover" name="' . $this->field['name'] . '[hover]' . $this->field['name_suffix'] . '" value="' . $this->value['hover'] . '" class="redux-color redux-color-hover redux-color-init ' . $this->field['class'] . '"  type="text" data-default-color="' . $this->field['default']['hover'] . '" /></span>';
        }

        if ($this->field['visited'] === true && $this->field['default']['visited'] !== false) {
            echo '<span class="linkColor"><strong>' . __(
                    'Visited',
                    'mozart-options'
                ) . '</strong>&nbsp;<input id="' . $this->field['id'] . '-hover" name="' . $this->field['name'] . '[visited]' . $this->field['name_suffix'] . '" value="' . $this->value['visited'] . '" class="redux-color redux-color-visited redux-color-init ' . $this->field['class'] . '"  type="text" data-default-color="' . $this->field['default']['visited'] . '" /></span>';
        }

        if ($this->field['active'] === true && $this->field['default']['active'] !== false) {
            echo '<span class="linkColor"><strong>' . __(
                    'Active',
                    'mozart-options'
                ) . '</strong>&nbsp;<input id="' . $this->field['id'] . '-active" name="' . $this->field['name'] . '[active]' . $this->field['name_suffix'] . '" value="' . $this->value['active'] . '" class="redux-color redux-color-active redux-color-init ' . $this->field['class'] . '"  type="text" data-default-color="' . $this->field['default']['active'] . '" /></span>';
        }
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
            'redux-field-link-color-js',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/link_color/field_link_color.js',
            array( 'jquery', 'wp-color-picker', 'redux-js' ),
            time(),
            true
        );

        wp_enqueue_style(
            'redux-field-link_color-js',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/link_color/field_link_color.css',
            time(),
            true
        );
    }

    public function output()
    {
        $style = array();

        if (!empty( $this->value['regular'] ) && $this->field['regular'] === true && $this->field['default']['regular'] !== false) {
            $style[] = 'color:' . $this->value['regular'] . ';';
        }

        if (!empty( $this->value['hover'] ) && $this->field['hover'] === true && $this->field['default']['hover'] !== false) {
            $style['hover'] = 'color:' . $this->value['hover'] . ';';
        }

        if (!empty( $this->value['active'] ) && $this->field['active'] === true && $this->field['default']['active'] !== false) {
            $style['active'] = 'color:' . $this->value['active'] . ';';
        }

        if (!empty( $this->value['visited'] ) && $this->field['visited'] === true && $this->field['default']['visited'] !== false) {
            $style['visited'] = 'color:' . $this->value['visited'] . ';';
        }

        if (!empty( $style )) {
            if (!empty( $this->field['output'] ) && is_array( $this->field['output'] )) {
                $styleString = "";

                foreach ($style as $key => $value) {
                    if (is_numeric( $key )) {
                        $styleString .= implode( ",", $this->field['output'] ) . "{" . $value . '}';
                    } else {
                        if (count( $this->field['output'] ) == 1) {
                            $styleString .= $this->field['output'][0] . ":" . $key . "{" . $value . '}';
                        } else {
                            $blah = '';
                            foreach ($this->field['output'] as $k => $sel) {
                                $blah .= $sel . ':' . $key . ',';
                            }

                            $blah = substr( $blah, 0, strlen( $blah ) - 1 );
                            $styleString .= $blah . '{' . $value . '}';

                        }
                    }
                }

                $this->parent->outputCSS .= $styleString;
            }

            if (!empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] )) {
                $styleString = "";

                foreach ($style as $key => $value) {
                    if (is_numeric( $key )) {
                        $styleString .= implode( ",", $this->field['compiler'] ) . "{" . $value . '}';

                    } else {
                        if (count( $this->field['compiler'] ) == 1) {
                            $styleString .= $this->field['compiler'][0] . ":" . $key . "{" . $value . '}';
                        } else {
                            $blah = '';
                            foreach ($this->field['compiler'] as $k => $sel) {
                                $blah .= $sel . ':' . $key . ',';
                            }

                            $blah = substr( $blah, 0, strlen( $blah ) - 1 );
                            $styleString .= $blah . '{' . $value . '}';
                        }
                    }
                }
                $this->parent->compilerCSS .= $styleString;
            }
        }
    }
}