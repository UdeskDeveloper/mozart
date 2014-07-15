<?php
namespace Mozart\Component\Option\Fields;

use Mozart\Component\Option\Field;

class Info extends Field
{
    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     * @return      void
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
     * @return      void
     */
    public function render()
    {
        $defaults = array(
            'title'  => '',
            'desc'   => '',
            'notice' => false,
            'style'  => ''
        );
        $this->field = wp_parse_args( $this->field, $defaults );

        if (empty( $this->field['desc'] ) && !empty( $this->field['default'] )) {
            $this->field['desc'] = $this->field['default'];
            unset( $this->field['default'] );
        }

        if (empty( $this->field['desc'] ) && !empty( $this->field['subtitle'] )) {
            $this->field['desc'] = $this->field['subtitle'];
            unset( $this->field['subtitle'] );
        }

        if (empty( $this->field['desc'] )) {
            $this->field['desc'] = "";
        }

        if (empty( $this->field['raw_html'] )) {
            if ($this->field['notice'] == true) {
                $this->field['class'] .= ' redux-notice-field';
            } else {
                $this->field['class'] .= ' redux-info-field';
            }

            if (empty( $this->field['style'] )) {
                $this->field['style'] = 'normal';
            }

            $this->field['style'] = 'redux-' . $this->field['style'] . ' ';
        }

        $indent = ( isset( $this->field['sectionIndent'] ) && $this->field['sectionIndent'] ) ? ' form-table-section-indented' : '';

        echo '</td></tr></table><div id="info-' . $this->field['id'] . '" class="' . $this->field['style'] . $this->field['class'] . ' redux-field-' . $this->field['type'] . $indent . '">';

        if (!empty( $this->field['raw_html'] ) && $this->field['raw_html']) {
            echo $this->field['desc'];
        } else {
            if (isset( $this->field['title'] ) && !empty( $this->field['title'] )) {
                $this->field['title'] = '<b>' . $this->field['title'] . '</b><br/>';
            }

            if (isset( $this->field['icon'] ) && !empty( $this->field['icon'] ) && $this->field['icon'] !== true) {
                echo '<p class="redux-info-icon"><i class="' . $this->field['icon'] . ' icon-large"></i></p>';
            }

            if (isset( $this->field['raw'] ) && !empty( $this->field['raw'] )) {
                echo $this->field['raw'];
            }

            if (!empty( $this->field['title'] ) || !empty( $this->field['desc'] )) {
                echo '<p class="redux-info-desc">' . $this->field['title'] . $this->field['desc'] . '</p>';
            }
        }

        echo '</div><table class="form-table no-border" style="margin-top: 0;"><tbody><tr style="border-bottom:0;"><th style="padding-top:0;"></th><td style="padding-top:0;">';
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     * @return      void
     */
    public function enqueue()
    {
        wp_enqueue_style(
            'redux-field-info-css',
            \Mozart::parameter( 'wp.plugin.uri' ) . '/mozart/public/bundles/mozart/option/fields/info/field_info.css',
            time(),
            true
        );
    }
}
