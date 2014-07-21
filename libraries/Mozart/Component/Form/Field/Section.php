<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Section extends Field
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
    }

    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @return void
     */
    public function render()
    {
        // No errors please
        $defaults = array(
            'indent' => '',
            'style' => '',
            'class' => '',
            'title' => '',
            'subtitle' => '',
        );
        $this->field = wp_parse_args( $this->field, $defaults );

        $guid = uniqid();

        $add_class = '';
        if (isset( $this->field['indent'] ) && !empty( $this->field['indent'] )) {
            $add_class = ' form-table-section-indented';
        }

        echo '<input type="hidden" id="' . $this->field['id'] . '-marker"></td></tr></table>';

        echo '<div id="section-' . $this->field['id'] . '" class="redux-section-field redux-field ' . $this->field['style'] . $this->field['class'] . '">';

        if (!empty( $this->field['title'] )) {
            echo '<h3>' . $this->field['title'] . '</h3>';
        }

        if (!empty( $this->field['subtitle'] )) {
            echo '<div class="redux-section-desc">' . $this->field['subtitle'] . '</div>';
        }

        echo '</div><table id="section-table-' . $this->field['id'] . '" class="form-table form-table-section no-border' . $add_class . '"><tbody><tr><th></th><td id="' . $guid . '">';

        // delete the tr afterwards
        ?>
        <script type="text/javascript">
            jQuery( document ).ready(
                function () {
                    jQuery( '#<?php echo $this->field['id']; ?>-marker' ).parents( 'tr:first' ).css( {display: 'none'} );
                }
            );
        </script>
    <?php

    }

    public function enqueue()
    {
        wp_enqueue_style(
            'redux-field-section-css',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/section/field_section.css',
            time(),
            true
        );
    }
}
