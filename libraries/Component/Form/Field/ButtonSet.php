<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class ButtonSet extends Field
{
    /**
     * Holds configuration settings for each field in a model.
     * Defining the field options
     * array['fields']              array Defines the fields to be shown by scaffolding.
     *          [fieldName]         array Defines the options for a field, or just enables the field if array is not applied.
     *              ['name']        string Overrides the field name (default is the array key)
     *              ['model']       string (optional) Overrides the model if the field is a belongsTo associated value.
     *              ['width']       string Defines the width of the field for paginate views. Examples are "100px" or "auto"
     *              ['align']       string Alignment types for paginate views (left, right, center)
     *              ['format']      string Formatting options for paginate fields. Options include ('currency','nice','niceShort','timeAgoInWords' or a valid Date() format)
     *              ['title']       string Changes the field name shown in views.
     *              ['desc']        string The description shown in edit/create views.
     *              ['readonly']    boolean True prevents users from changing the value in edit/create forms.
     *              ['type']        string Defines the input type used by the Form helper (example 'password')
     *              ['options']     array Defines a list of string options for drop down lists.
     *              ['editor']      boolean If set to True will show a WYSIWYG editor for this field.
     *              ['default']     string The default value for create forms.
     *
     * @param array $arr (See above)
     *
     * @return Object A new editor object.
     *                */
    static $_properties = array(
        'id' => 'Identifier',
    );

    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     *
     *
     * @return void
     */
    public function render()
    {
        // multi => true renders the field multi-selectable (checkbox vs radio)
        echo '<div class="buttonset ui-buttonset">';

        //$i = 0;
        foreach ($this->field['options'] as $k => $v) {

            $selected = '';
            if (isset( $this->field['multi'] ) && $this->field['multi'] == true) {
                $type = "checkbox";
                $this->field['name_suffix'] = "[]";
//                    $i++;

                if (!empty( $this->value ) && !is_array( $this->value )) {
                    $this->value = array( $this->value );
                }

                if (in_array( $k, $this->value )) {
                    $selected = 'checked="checked"';
                }
            } else {
                $type = "radio";
                $selected = checked( $this->value, $k, false );
            }

            echo '<input data-id="' . $this->field['id'] . '" type="' . $type . '" id="' . $this->field['id'] . '-buttonset' . $k . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '" class="buttonset-item ' . $this->field['class'] . '" value="' . $k . '" ' . $selected . '/>';
            echo '<label for="' . $this->field['id'] . '-buttonset' . $k . '">' . $v . '</label>';

            if (isset( $this->field['multi'] ) && $this->field['multi'] == true) {
                echo '<input type="hidden" id="' . $this->field['id'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '" value="">';
            }
        }

        echo '</div>';
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     * @return void
     */
    public function enqueue()
    {
        wp_enqueue_script(
            'redux-field-button-set-js',
            \Mozart::parameter('wp.plugin.uri') . '/mozart/public/bundles/mozart/form/fields/button_set/field_button_set.js',
            array( 'jquery', 'jquery-ui-core', 'redux-js' ),
            time(),
            true
        );
    }
}
