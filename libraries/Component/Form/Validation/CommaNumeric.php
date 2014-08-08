<?php

namespace Mozart\Component\Form\Validation;

class CommaNumeric
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     *
     */
    public function __construct($parent, $field, $value, $current)
    {
        $this->parent = $parent;
        $this->field = $field;
        $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : __(
            'You must provide a comma separated list of numerical values for this option.',
            'mozart-options'
        );
        $this->value = $value;
        $this->current = $current;

        $this->validate();
    }

    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     *
     */
    public function validate()
    {
        $this->value = str_replace( ' ', '', $this->value );

        if (!is_numeric( str_replace( ',', '', $this->value ) )) {
            $this->value = ( isset( $this->current ) ) ? $this->current : '';
            $this->error = $this->field;
        }
    }
}
