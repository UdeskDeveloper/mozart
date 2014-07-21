<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Raw extends Field
{
    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     */
    public function render()
    {
        // If align value is not set, set it to false, the default
        if (!isset( $this->field['align'] )) {
            $this->field['align'] = false;
        }

        // Set align flag.
        $doAlign = $this->field['align'];

        // The following could needs to be omitted if align is true.
        // Only print it if allign is false.
        if (false == $doAlign) {
            echo '<style>#' . $this->builder->args['opt_name'] . '-' . $this->field['id'] . ' {padding: 0;}</style>';
            echo '</td></tr></table><table class="form-table no-border redux-group-table redux-raw-table" style="margin-top: -20px; overflow: auto;"><tbody><tr><td>';
        }

        echo '<fieldset id="' . $this->builder->args['opt_name'] . '-' . $this->field['id'] . '" class="redux-field redux-container-' . $this->field['type'] . ' ' . $this->field['class'] . '" data-id="' . $this->field['id'] . '">';

        if (!empty( $this->field['include'] ) && file_exists( $this->field['include'] )) {
            include( $this->field['include'] );
        }

        if (!empty( $this->field['content'] ) && isset( $this->field['content'] )) {
            if (isset( $this->field['markdown'] ) && $this->field['markdown'] == true) {
                echo \Mozart::service( 'markdown.parser' )->transformMarkdown( $this->field['content'] );
            } else {
                echo $this->field['content'];
            }
        }

        do_action( 'redux-field-raw-' . $this->builder->args['opt_name'] . '-' . $this->field['id'] );

        echo '</fieldset>';

        // Only print is align is false.
        if (false == $doAlign) {
            echo '</td></tr></table><table class="form-table no-border" style="margin-top: 0;"><tbody><tr style="border-bottom: 0;"><th></th><td>';
        }
    }
}
