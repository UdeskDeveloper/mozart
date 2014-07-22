<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

/**
 * Class Slider
 * @package Mozart\Component\Form\Field
 */
class Slider extends Field
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     */
    private $display_none = 0;
    /**
     * @var int
     */
    private $display_label = 1;
    /**
     * @var int
     */
    private $display_text = 2;
    /**
     * @var int
     */
    private $display_select = 3;

    /**
     *
     */
    protected function initialize()
    {

        // Set defaults
        $defaults = array(
            'handles'       => 1,
            'resolution'    => 1,
            'display_value' => 'text',
            'float_mark'    => '.',
        );

        $this->field = wp_parse_args( $this->field, $defaults );

        // Sanitize float mark
        switch ($this->field['float_mark']) {
            case ',':
            case '.':
                break;
            default:
                $this->field['float_mark'] = '.';
                break;
        }

        // Sanitize resolution value
        $this->field['resolution'] = $this->cleanVal( $this->field['resolution'] );

        // Sanitize handle value
        switch ($this->field['handles']) {
            case 0:
            case 1:
                $this->field['handles'] = 1;
                break;
            default:
                $this->field['handles'] = 2;
                break;
        }

        // Sanitize display value
        switch ($this->field['display_value']) {
            case 'label':
                $this->field['display_value'] = $this->display_label;
                break;
            case 'text':
            default:
                $this->field['display_value'] = $this->display_text;
                break;
            case 'select':
                $this->field['display_value'] = $this->display_select;
                break;
            case 'none':
                $this->field['display_value'] = $this->display_none;
                break;
        }
    }


    /**
     * @param $var
     * @return float|int
     */
    private function cleanVal( $var )
    {
        if (is_float( $var )) {
            $cleanVar = floatval( $var );
        } else {
            $cleanVar = intval( $var );
        }

        return $cleanVar;
    }

    /**
     * @param $val
     * @return float|int
     */
    private function cleanDefault( $val )
    {
        if (empty( $val ) && !empty( $this->field['default'] ) && $this->cleanVal( $this->field['min'] ) >= 1) {
            $val = $this->cleanVal( $this->field['default'] );
        }

        if (empty( $val ) && $this->cleanVal( $this->field['min'] ) >= 1) {
            $val = $this->cleanVal( $this->field['min'] );
        }

        if (empty( $val )) {
            $val = 0;
        }

        // Extra Validation
        if ($val < $this->field['min']) {
            $val = $this->cleanVal( $this->field['min'] );
        } elseif ($val > $this->field['max']) {
            $val = $this->cleanVal( $this->field['max'] );
        }

        return $val;
    }

    /**
     * @param $val
     * @return mixed
     */
    private function cleanDefaultArray( $val )
    {
        $one = $this->value[1];
        $two = $this->value[2];

        if (empty( $one ) && !empty( $this->field['default'][1] ) && $this->cleanVal( $this->field['min'] ) >= 1) {
            $one = $this->cleanVal( $this->field['default'][1] );
        }

        if (empty( $one ) && $this->cleanVal( $this->field['min'] ) >= 1) {
            $one = $this->cleanVal( $this->field['min'] );
        }

        if (empty( $one )) {
            $one = 0;
        }

        if (empty( $two ) && !empty( $this->field['default'][2] ) && $this->cleanVal( $this->field['min'] ) >= 1) {
            $two = $this->cleanVal( $this->field['default'][1] + 1 );
        }

        if (empty( $two ) && $this->cleanVal( $this->field['min'] ) >= 1) {
            $two = $this->cleanVal( $this->field['default'][1] + 1 );
        }

        if (empty( $two )) {
            $two = $this->field['default'][1] + 1;
        }

        $val[0] = $one;
        $val[1] = $two;

        return $val;
    }

    /**
     * Clean the field data to the fields defaults given the parameters.
     */
    public function clean()
    {
        // Set min to 0 if no value is set.
        $this->field['min'] = empty( $this->field['min'] ) ? 0 : $this->cleanVal( $this->field['min'] );

        // Set max to min + 1 if empty.
        $this->field['max'] = empty( $this->field['max'] ) ? $this->field['min'] + 1 : $this->cleanVal(
            $this->field['max']
        );

        // Set step to 1 if step is empty ot step > max.
        $this->field['step'] = empty( $this->field['step'] ) || $this->field['step'] > $this->field['max'] ? 1 : $this->cleanVal(
            $this->field['step']
        );

        if (2 == $this->field['handles']) {
            if (!is_array( $this->value )) {
                $this->value[1] = 0;
                $this->value[2] = 1;
            }
            $this->value = $this->cleanDefaultArray( $this->value );
        } else {
            if (is_array( $this->value )) {
                $this->value = 0;
            }
            $this->value = $this->cleanDefault( $this->value );
        }

        if (!is_array( $this->value ) && 2 == $this->field['handles']) {
            $this->value[0] = $this->field['min'];
            $this->value[1] = $this->field['min'] + 1;
        }

        if (is_array( $this->value ) && 1 == $this->field['handles']) {
            $this->value = $this->field['min'];
        }

    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     */
    public function enqueue()
    {
        wp_enqueue_style(
            'nouislider-css',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/form/fields/slider/vendor/nouislider/jquery.nouislider.css',
            array(),
            filemtime(
                \Mozart::parameter(
                    'wp.plugin.dir'
                ) . '/mozart/public/bundles/mozart/form/fields/slider/vendor/nouislider/jquery.nouislider.css'
            ),
            'all'
        );

        wp_register_script(
            'nouislider-js',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/form/fields/slider/vendor/nouislider/jquery.nouislider.js',
            array( 'jquery' ),
            '5.0.0',
            true
        );

        wp_enqueue_script(
            'redux-field-slider-js',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/form/fields/slider/field_slider.js',
            array( 'jquery', 'nouislider-js', 'redux-js', 'select2-js' ),
            time(),
            true
        );

        wp_enqueue_style(
            'redux-field-slider-css',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/form/fields/slider/field_slider.css',
            time(),
            true
        );
    }

    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     */
    public function render()
    {
        $this->clean();

        $fieldID = $this->field['id'];
        $fieldName = $this->field['name'];
        //$fieldName = $this->builder->getParam('opt_name') . '[' . $this->field['id'] . ']';

        // Set handle number variable.
        $twoHandles = false;
        if (2 == $this->field['handles']) {
            $twoHandles = true;
        }

        // Set default values(s)
        if (true == $twoHandles) {
            $valOne = $this->value[0];
            $valTwo = $this->value[1];

            $html = 'data-default-one="' . $valOne . '" ';
            $html .= 'data-default-two="' . $valTwo . '" ';

            $nameOne = $fieldName . '[1]';
            $nameTwo = $fieldName . '[2]';

            $idOne = $fieldID . '[1]';
            $idTwo = $fieldID . '[2]';
        } else {
            $valOne = $this->value;
            $valTwo = '';

            $html = 'data-default-one="' . $valOne . '"';

            $nameOne = $fieldName;
            $nameTwo = '';

            $idOne = $fieldID;
            $idTwo = '';
        }

        $showInput = false;
        $showLabel = false;
        $showSelect = false;

        // TEXT output
        if ($this->display_text == $this->field['display_value']) {
            $showInput = true;
            echo '<input type="text"
                             name="' . $nameOne . $this->field['name_suffix'] . '"
                             id="' . $idOne . '"
                             value="' . $valOne . '"
                             class="redux-slider-input redux-slider-input-one-' . $fieldID . ' ' . $this->field['class'] . '"/>';

            // LABEL output
        } elseif ($this->display_label == $this->field['display_value']) {
            $showLabel = true;

            $labelNum = $twoHandles ? '-one' : '';

            echo '<div class="redux-slider-label' . $labelNum . '"
                           id="redux-slider-label-one-' . $fieldID . '"
                           name="' . $nameOne . $this->field['name_suffix'] . '">
                      </div>';

            // SELECT output
        } elseif ($this->display_select == $this->field['display_value']) {
            $showSelect = true;

            if (isset( $this->field['select2'] )) { // if there are any let's pass them to js
                $select2_params = json_encode( $this->field['select2'] );
                $select2_params = htmlspecialchars( $select2_params, ENT_QUOTES );

                echo '<input type="hidden" class="select2_params" value="' . $select2_params . '">';
            }


            echo '<select class="redux-slider-select-one redux-slider-select-one-' . $fieldID . ' ' . $this->field['class'] . '"
                              name="' . $nameOne . $this->field['name_suffix'] . '"
                              id="' . $idOne . '">
                     </select>';
        }

        // DIV output
        echo '<div ' . '
                    class="redux-slider-container"' . ' ' . $this->field['class'] . '
                    id="' . $fieldID . '" ' . '
                    data-id="' . $fieldID . '" ' . '
                    data-min="' . $this->field['min'] . '" ' . '
                    data-max="' . $this->field['max'] . '" ' . '
                    data-step="' . $this->field['step'] . '" ' . '
                    data-handles="' . $this->field['handles'] . '" ' . '
                    data-display="' . $this->field['display_value'] . '" ' . '
                    data-rtl="' . is_rtl() . '" ' . '
                    data-float-mark="' . $this->field['float_mark'] . '" ' . '
                    data-resolution="' . $this->field['resolution'] . '" ' . $html . '>' . '
                    </div>';

        // Double slider output
        if (true == $twoHandles) {

            // TEXT
            if (true == $showInput) {
                echo '<input type="text"
                                 name="' . $nameTwo . $this->field['name_suffix'] . '"
                                 id="' . $idTwo . '"
                                 value="' . $valTwo . '"
                                 class="redux-slider-input redux-slider-input-two-' . $fieldID . ' ' . $this->field['class'] . '"/>';
            }

            // LABEL
            if (true == $showLabel) {
                echo '<div class="redux-slider-label-two"
                               id="redux-slider-label-two-' . $fieldID . '"
                               name="' . $nameTwo . $this->field['name_suffix'] . '">
                          </div>';
            }

            // SELECT
            if (true == $showSelect) {
                echo '<select class="redux-slider-select-two redux-slider-select-two-' . $fieldID . ' ' . $this->field['class'] . '"
                                  name="' . $nameTwo . $this->field['name_suffix'] . '"
                                  id="' . $idTwo . '">
                         </select>';

            }
        }

        // NO output (input hidden)
        if ($this->display_none == $this->field['display_value'] || $this->display_label == $this->field['display_value']) {
            echo '<input type="hidden"
                             class="redux-slider-value-one-' . $fieldID . ' ' . $this->field['class'] . '"
                             name="' . $nameOne . $this->field['name_suffix'] . '"
                             id="' . $idOne . '"
                             value="' . $valOne . '"/>';

            // double slider hidden output
            if (true == $twoHandles) {
                echo '<input type="hidden"
                                 class="redux-slider-value-two-' . $fieldID . ' ' . $this->field['class'] . '"
                                 name="' . $nameTwo . $this->field['name_suffix'] . '"
                                 id="' . $idTwo . '"
                                 value="' . $valTwo . '"/>';
            }
        }
    }
}
