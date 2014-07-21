<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Divide extends Field
{
    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @return void
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
