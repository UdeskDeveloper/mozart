<?php

namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class ImportExport extends Field
{
    protected function initialize()
    {
        if (!isset( $this->field['full_width'] )) {
            $this->field['full_width'] = true;
        }

        $args = array(
            'full_width' => $this->field['full_width']
        );

        $this->builder->import_export->field_args = $args;
    }


    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @return void
     */
    public function render()
    {
        $this->builder->import_export->render();
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     * @return void
     */
    public function enqueue()
    {
        $this->builder->import_export->enqueue();
    }
}
