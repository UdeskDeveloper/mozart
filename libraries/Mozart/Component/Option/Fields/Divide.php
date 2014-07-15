<?php
namespace Mozart\Component\Option\Fields;

use Mozart\Component\Option\Field;

class Divide extends Field
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and
     * value to vars, and obviously call the render field function
     *
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
     * @return        void
     */
    public function render()
    {
        echo '</td></tr></table>';
        echo '<div data-id="' . $this->field['id'] . '" id="divide-' . $this->field['id']
            . '" class="hr ' . $this->field['class']
            . '"/><div class="inner"><span>&nbsp;</span></div></div>';
        echo '<table class="form-table no-border"><tbody><tr><th></th><td>';
    }
}

