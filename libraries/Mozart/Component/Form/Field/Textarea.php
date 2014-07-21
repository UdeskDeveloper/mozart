<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

/**
 * Class Textarea
 * @package Mozart\Component\Form\Field
 */
class Textarea extends Field
{
    /**
     * @param array $field
     * @param string $value
     * @param $parent
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
     * @param array $arr (See above)
     *
     * @return Object A new editor object.
     **/
    public function render()
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
