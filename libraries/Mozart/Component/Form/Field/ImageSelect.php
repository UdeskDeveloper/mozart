<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class ImageSelect extends Field
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     *
     *
     * @return void
     */
    public function __construct( $field = array(), $value = '', $parent )
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
     * @return void
     */
    public function render()
    {
        if (!empty( $this->field['options'] )) {
            echo '<div class="redux-table-container">';
            echo '<ul class="redux-image-select">';

            $x = 1;

            foreach ($this->field['options'] as $k => $v) {

                if (!is_array( $v )) {
                    $v = array( 'img' => $v );
                }

                if (!isset( $v['title'] )) {
                    $v['title'] = '';
                }

                if (!isset( $v['alt'] )) {
                    $v['alt'] = $v['title'];
                }

                $style = '';

                if (!empty( $this->field['width'] )) {
                    $style .= 'width: ' . $this->field['width'];

                    if (is_numeric( $this->field['width'] )) {
                        $style .= 'px';
                    }

                    $style .= ';';
                } else {
                    $style .= " width: 100%; ";
                }

                if (!empty( $this->field['height'] )) {
                    $style .= 'height: ' . $this->field['height'];

                    if (is_numeric( $this->field['height'] )) {
                        $style .= 'px';
                    }

                    $style .= ';';
                }

                $theValue = $k;
                if (!empty( $this->field['tiles'] ) && $this->field['tiles'] == true) {
                    $theValue = $v['img'];
                }

                $selected = ( checked( $this->value, $theValue, false ) != '' ) ? ' redux-image-select-selected' : '';

                $presets = '';
                $is_preset = false;

                $this->field['class'] .= ' noUpdate ';
                if (isset( $this->field['presets'] ) && $this->field['presets'] !== false) {
                    $this->field['class'] = trim( $this->field['class'] );
                    if (!isset( $v['presets'] )) {
                        $v['presets'] = array();
                    }

                    if (!is_array( $v['presets'] )) {
                        $v['presets'] = json_decode( $v['presets'], true );
                    }

                    // Only highlight the preset if it's the same
                    if ($selected) {
                        if (empty( $v['presets'] )) {
                            $selected = false;
                        } else {
                            foreach ($v['presets'] as $pk => $pv) {
                                if (empty( $pv ) && isset( $this->parent->options[$pk] ) && !empty( $this->parent->options[$pk] )) {
                                    $selected = false;
                                } elseif (!empty( $pv ) && !isset( $this->parent->options[$pk] )) {
                                    $selected = false;
                                } elseif (isset( $this->parent->options[$pk] ) && $this->parent->options[$pk] != $pv) {
                                    $selected = false;
                                }

                                if (!$selected) { // We're still not using the same preset. Let's unset that shall we?
                                    $this->value = "";
                                    break;
                                }
                            }
                        }
                    }

                    $v['presets']['redux-backup'] = 1;

                    $presets = ' data-presets="' . htmlspecialchars(
                            json_encode( $v['presets'] ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) . '"';
                    $is_preset = true;

                    $this->field['class'] = trim( $this->field['class'] ) . 'redux-presets';
                }

                $is_preset_class = $is_preset ? '-preset-' : ' ';

                echo '<li class="redux-image-select">';
                echo '<label class="' . $selected . ' redux-image-select' . $is_preset_class . $this->field['id'] . '_' . $x . '" for="' . $this->field['id'] . '_' . ( array_search(
                            $k,
                            array_keys( $this->field['options'] )
                        ) + 1 ) . '">';

                echo '<input type="radio" class="' . $this->field['class'] . '" id="' . $this->field['id'] . '_' . ( array_search(
                            $k,
                            array_keys( $this->field['options'] )
                        ) + 1 ) . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '" value="' . $theValue . '" ' . checked(
                        $this->value,
                        $theValue,
                        false
                    ) . $presets . '/>';
                if (!empty( $this->field['tiles'] ) && $this->field['tiles'] == true) {
                    echo '<span class="tiles" style="background-image: url(' . $v['img'] . ');" rel="' . $v['img'] . '"">&nbsp;</span>';
                } else {
                    echo '<img src="' . $v['img'] . '" alt="' . $v['alt'] . '" style="' . $style . '"' . $presets . ' />';
                }

                if ($v['title'] != '') {
                    echo '<br /><span>' . $v['title'] . '</span>';
                }

                echo '</label>';
                echo '</li>';

                $x++;
            }

            echo '</ul>';
            echo '</div>';
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
        wp_enqueue_script(
            'redux-field-image-select-js',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/image_select/field_image_select.js',
            array( 'jquery', 'redux-js' ),
            time(),
            true
        );

        wp_enqueue_style(
            'redux-field-image-select-css',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/image_select/field_image_select.css',
            time(),
            true
        );
    }

    public function getCSS($mode = '')
    {
        $css = '';
        $value = $this->value;

        if (!empty( $value )) {
            switch ($mode) {
                case 'background-image':
                    $output = "background-image: url('" . $value . "');";
                    break;

                default:
                    $output = $mode . ": " . $value . ";";
            }
        }

        $css .= $output;

        return $css;
    }

    public function output()
    {
        $mode = ( isset( $this->field['mode'] ) && !empty( $this->field['mode'] ) ? $this->field['mode'] : 'background-image' );

        if (( !isset( $this->field['output'] ) || !is_array(
                    $this->field['output']
                ) ) && ( !isset( $this->field['compiler'] ) )
        ) {
            return;
        }

        $style = $this->getCSS( $mode );

        if (!empty( $style )) {

            if (!empty( $this->field['output'] ) && is_array( $this->field['output'] )) {
                $keys = implode( ",", $this->field['output'] );
                $style = $keys . "{" . $style . '}';
                $this->parent->outputCSS .= $style;
            }

            if (!empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] )) {
                $keys = implode( ",", $this->field['compiler'] );
                $style = $keys . "{" . $style . '}';
                $this->parent->compilerCSS .= $style;
            }
        }
    }
}
