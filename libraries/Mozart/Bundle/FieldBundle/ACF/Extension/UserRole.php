<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\FieldBundle\ACF\Extension;


class UserRole  extends \acf_field {

    /*
    *  __construct
    *
    *  This function will setup the field type data
    *
    *  @type	function
    *  @date	5/03/2014
    *  @since	5.0.0
    *
    *  @param	n/a
    *  @return	n/a
    */

    /**
     *
     */
    public function __construct()
    {

        /*
        *  name (string) Single word, no spaces. Underscores allowed
        */

        $this->name = 'user_role';

        /*
        *  label (string) Multiple words, can include spaces, visible when selecting a field type
        */

        $this->label = __( 'User Role Selector', 'acf' );

        /*
        *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
        */

        $this->category = 'relational';

        /*
        *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
        */

        $this->defaults = array(
            'return_value' => 'name',
            'field_type'   => 'checkbox',
        );

        /*
        *  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
        *  var message = acf._e('nav_menu', 'error');
        */

        // do not delete!
        parent::__construct();
    }

    /*
    *  render_field_settings()
    *
    *  Create extra settings for your field. These are visible when editing a field
    *
    *  @type	action
    *  @since	3.6
    *  @date	23/01/13
    *
    *  @param	$field (array) the $field being edited
    *  @return	n/a
    */

    /**
     * @param $field
     */
    public function render_field_settings($field)
    {

        /*
        *  acf_render_field_setting
        *
        *  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
        *  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
        *
        *  More than one setting can be added by copy/paste the above code.
        *  Please note that you must also have a matching $defaults value for the field name (font_size)
        */

        acf_render_field_setting(
            $field,
            array(
                'label'        => __( 'Return Format', 'acf' ),
                'instructions' => __( 'Specify the returned value on front end', 'acf' ),
                'type'         => 'radio',
                'name'         => 'return_value',
                'layout'       => 'horizontal',
                'choices' =>  array(
                    'name'   => __( 'Role Name', 'acf' ),
                    'object' => __( 'Role Object', 'acf' ),
                )
            )
        );
        acf_render_field_setting(
            $field,
            array(
                'label'        => __( 'Field Type', 'acf' ),
                'instructions' => __( '', 'acf' ),
                'type'         => 'select',
                'name'         => 'field_type',
                'choices' => array(
                    __( 'Multiple Values', 'acf' ) => array(
                        'checkbox' => __( 'Checkbox', 'acf' ),
                        'multi_select' => __( 'Multi Select', 'acf' )
                    ),
                    __( 'Single Value', 'acf' ) => array(
                        'radio' => __( 'Radio Buttons', 'acf' ),
                        'select' => __( 'Select', 'acf' )
                    )
                )
            )
        );

    }

    /*
    *  render_field()
    *
    *  Create the HTML interface for your field
    *
    *  @param	$field (array) the $field being rendered
    *
    *  @type	action
    *  @since	3.6
    *  @date	23/01/13
    *
    *  @param	$field (array) the $field being edited
    *  @return	n/a
    */

    /**
     * @param $field
     */
    public function render_field($field)
    {
        global $wp_roles;
        $all_roles = $wp_roles->roles;

        $selected_roles = array();
        if( !empty( $field['value'] ) && 'object' == $field['return_value'] ) {
            foreach( $field['value'] as $value ) {
                $selected_roles[] = $value->name;
            }
        }
        else {
            $selected_roles = $field['value'];
        }


        if( $field['field_type'] == 'select' || $field['field_type'] == 'multi_select' ) :
            $multiple = ( $field['field_type'] == 'multi_select' ) ? 'multiple="multiple"' : '';
            ?>

            <select name='<?php echo $field['name'] ?>[]' <?php echo $multiple ?>>
                <?php
                foreach( $all_roles as $role => $data ) :
                    $selected = ( in_array( $role, $selected_roles ) ) ? 'selected="selected"' : '';
                    ?>
                    <option <?php echo $selected ?> value='<?php echo $role ?>'><?php echo $data['name'] ?></option>
                <?php endforeach; ?>

            </select>
        <?php
        elseif( $field['field_type'] == 'radio' ) :
            echo '<ul class="acf-radio-list radio vertical">';
            foreach( $all_roles as $role => $data ) :
                $checked = ( in_array( $role, $selected_roles ) ) ? 'checked="checked"' : '';
                ?>
                <label><input <?php echo $checked ?> type="radio" name="<?php echo $field['name'] ?>" value="<?php echo $role ?>"><?php echo $data['name'] ?></label>
            <?php
            endforeach;
            echo '</ul>';
        else :
            echo '<ul class="acf-checkbox-list checkbox vertical">';
            foreach( $all_roles as $role => $data ) :
                $checked = ( in_array( $role, $selected_roles ) ) ? 'checked="checked"' : '';
                ?>
                <li><label><input <?php echo $checked ?> type="checkbox" class="checkbox" name="<?php echo $field['name'] ?>[]" value="<?php echo $role ?>"><?php echo $data['name'] ?></label></li>
            <?php
            endforeach;
            echo '</ul>';
        endif;
    }


    /*
    *  format_value()
    *
    *  This filter is appied to the $value after it is loaded from the db and before it is passed to the create_field action
    *
    *  @type	filter
    *  @since	3.6
    *  @date	23/01/13
    *
    *  @param	$value	- the value which was loaded from the database
    *  @param	$post_id - the $post_id from which the value was loaded
    *  @param	$field	- the field array holding all the field options
    *
    *  @return	$value	- the modified value
    */

    function format_value($value, $post_id, $field)
    {
        if( $field['return_value'] == 'object' )
        {
            foreach( $value as $key => $name ) {
                $value[$key] = get_role( $name );
            }
        }
        return $value;
    }


    /*
    *  format_value_for_api()
    *
    *  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
    *
    *  @type	filter
    *  @since	3.6
    *  @date	23/01/13
    *
    *  @param	$value	- the value which was loaded from the database
    *  @param	$post_id - the $post_id from which the value was loaded
    *  @param	$field	- the field array holding all the field options
    *
    *  @return	$value	- the modified value
    */

    function format_value_for_api($value, $post_id, $field)
    {

        // format
        if( $field['return_value'] == 'object' )
        {
            foreach( $value as $key => $name ) {
                $value[$key] = get_role( $name );
            }
        }

        return $value;
    }
} 