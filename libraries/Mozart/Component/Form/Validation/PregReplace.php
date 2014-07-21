<?php

namespace Mozart\Component\Form\Validation;

class PregReplace
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     *
     */
    function __construct( $parent, $field, $value, $current )
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
    function validate()
    {
        $this->value = preg_replace(
            $this->field['preg']['pattern'],
            $this->field['preg']['replacement'],
            $this->value
        );
    }
}