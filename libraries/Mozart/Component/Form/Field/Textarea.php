<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Textarea extends Field
{
    /**
     * Field Constructor.
     *
     * @param       $value  Constructed by Redux class. Based on the passing in $field['defaults'] value and what is stored in the database.
     * @param       $parent ReduxFramework object is passed for easier pointing.
     *
     * @type string $field [test] Description. Default <value>. Accepts <value>, <value>.
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
     * @param array $arr (See above)
     *
     * @return Object A new editor object.
     **/
    function render()
    {
        $this->field['placeholder'] = isset( $this->field['placeholder'] ) ? $this->field['placeholder'] : "";
        $this->field['rows'] = isset( $this->field['rows'] ) ? $this->field['rows'] : 6;

        ?>
        <textarea name="<?php echo $this->field['name'] . $this->field['name_suffix']; ?>"
                  id="<?php echo $this->field['id']; ?>-textarea"
                  placeholder="<?php echo esc_attr( $this->field['placeholder'] ); ?>"
                  class="large-text <?php echo $this->field['class']; ?>"
                  rows="<?php echo $this->field['rows']; ?>"><?php echo $this->value; ?></textarea>
    <?php
    }
}