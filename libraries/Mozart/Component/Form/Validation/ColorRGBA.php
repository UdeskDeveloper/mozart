<?php
namespace Mozart\Component\Form\Validation;

class ColorRGBA
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     * @param $parent
     * @param $field
     * @param $value
     * @param $current
     */
    function __construct( $parent, $field, $value, $current )
    {
        $this->parent = $parent;
        $this->field = $field;
        $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : __(
            'This field must be a valid color value.',
            'mozart-options'
        );
        $this->value = $value;
        $this->current = $current;

        $this->validate();
    }

    /**
     * Validate Color to RGBA
     * Takes the user's input color value and returns it only if it's a valid color.
     * @param $color
     * @return array
     */
    function validate_color_rgba( $color )
    {
        if ($color == "transparent") {
            return $color;
        }

        $color = str_replace( '#', '', $color );
        if (strlen( $color ) == 3) {
            $color = $color . $color;
        }
        if (preg_match( '/^[a-f0-9]{6}$/i', $color )) {
            $color = '#' . $color;
        }

        return array( 'hex' => $color, 'rgba' => $this->hex2rgba( $color ) );
    }


    /**
     * Field Render Function.
     * Takes the color hex value and converts to a rgba.
     * @param $hex
     * @param string $alpha
     * @return string
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
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     *
     */
    function validate()
    {
        if (is_array( $this->value )) { // If array
            foreach ($this->value as $k => $value) {
                $this->value[$k] = $this->validate_color_rgba( $value );
            }
            //foreach
        } else { // not array
            $this->value = $this->validate_color_rgba( $this->value );
        } // END array check
    }
}
