<?php

namespace Mozart\Component\Form\Validation;

class StrReplace
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
        $this->value = $value;
        $this->current = $current;

        $this->validate();
    }

    /**
     * Field Render Function.
     * Takes the vars and validates them
     *
     *
     */
    public function validate()
    {
        $this->value = str_replace( $this->field['str']['search'], $this->field['str']['replacement'], $this->value );
    }
}
