<?php

namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class ImportExport extends Field
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     * @return void
     */
    public function __construct( $field = array(), $value = '', $parent )
    {
        $this->parent = $parent;
        $this->field = $field;
        $this->value = $value;

        if (!isset( $this->field['full_width'] )) {
            $this->field['full_width'] = true;
        }

        $args = array(
            'full_width' => $this->field['full_width']
        );

        $this->parent->import_export->field_args = $args;
    }

    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @return void
     */
    public function render()
    {
        $this->parent->import_export->render();
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     * @return void
     */
    public function enqueue()
    {
        $this->parent->import_export->enqueue();
    }
}
