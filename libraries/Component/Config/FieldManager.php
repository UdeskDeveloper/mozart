<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config;

use Mozart\Component\Support\Str;
use Symfony\Component\Debug\Exception\ClassNotFoundException;

/**
 * Class FieldManager
 * @package Mozart\Component\Config
 */
class FieldManager
{
    /**
     * @var array
     */
    private $hidden_perm_fields = array(); //  Hidden fields specified by 'permissions' arg.
    /**
     * @var array
     */
    private $compiler_fields = array(); // Fields that trigger the compiler hook
    /**
     * Fields by type used in the panel
     *
     * @var array
     */
    private $fields = array();

    /**
     * @var array
     */
    private $toHide = array(); // Values to hide on page load
    /**
     * @var array
     */
    private $folds = array(); // The items that need to fold.
    /**
     * @var array
     */
    private $required_child = array(); // Information that needs to be localized
    /**
     * @var array
     */
    private $required = array(); // Information that needs to be localized
    /**
     * @var array
     */
    private $fieldsValues = array(); //all fields values in an id=>value array so we can check dependencies
    /**
     * @var array
     */
    private $fieldsHidden = array(); //all fields that didn't pass the dependency test and are hidden

    /**
     * @var ConfigFactory
     */
    private $builder;

    public function addHiddenField($field_id, $data)
    {
        $this->hidden_perm_fields[$field_id] = $data;
    }

    public function addCompilerField($fieldId)
    {
        $this->compiler_fields[$fieldId] = 1;
    }

    /**
     * @param ConfigFactory $builder
     */
    public function init(ConfigFactory $builder)
    {
        $this->builder = $builder;
    }

    public function getFolds()
    {
        return $this->folds;
    }

    public function getRequired()
    {
        return $this->required;
    }

    public function getRequiredChild()
    {
        return $this->required_child;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $field
     */
    public function addField($field)
    {
        // Detect what field types are being used
        if (!isset( $this->fields[$field['type']][$field['id']] )) {
            $this->fields[$field['type']][$field['id']] = 1;
        } else {
            $this->fields[$field['type']] = array( $field['id'] => 1 );
        }
    }

    public function enqueueOutput($field)
    {
        if (!isset( $field['type'] ) || $field['type'] == "callback") {
            return;
        }

        $fieldClass = $this->getFieldClass( $field['type'] );

        if ($fieldClass && method_exists( $fieldClass, 'output' ) && $this->canOutputCSS( $field )
        ) {
            if (!isset( $field['compiler'] )) {
                $field['compiler'] = "";
            }

            if (!empty( $field['output'] ) && !is_array( $field['output'] )) {
                $field['output'] = array( $field['output'] );
            }

            $enqueue = new $fieldClass( $this->builder, $field, $this->builder->getOption( $field['id'] ) );

            if (( ( isset( $field['output'] ) &&
                    !empty( $field['output'] ) ) ||
                ( isset( $field['compiler'] ) &&
                    !empty( $field['compiler'] ) ) ||
                $field['type'] == "typography" ||
                $field['type'] == "icon_select" )
            ) {
                $enqueue->output();
            }
        }
    }

    public function enqueueScripts($field)
    {
        if (!isset( $field['type'] ) || $field['type'] == 'callback') {
            return;
        }

        $fieldClass = $this->getFieldClass( $field['type'] );

        if (!$fieldClass || false === method_exists( $fieldClass, 'enqueue' )) {
            return;
        }

        $theField = new $fieldClass( $this->builder, $field, $this->builder->getOption( $field['id'] ) );

        if (!wp_script_is(
            'redux-field-' . $field['type'] . '-js',
            'enqueued'
        )
        ) {
            $theField->enqueue();
        }

        unset( $theField );
    }

    public function localizeFieldData($field, $localizeData)
    {
        if (!isset( $field['type'] ) || $field['type'] == 'callback') {
            return $localizeData;
        }

        $fieldClass = $this->getFieldClass( $field['type'] );

        if (!$fieldClass || ( false === method_exists( $fieldClass, 'enqueue' )
                && false === method_exists( $fieldClass, 'localize' ) )
        ) {
            return $localizeData;
        }
        $theField = new $fieldClass( $this->builder, $field, $this->builder->getOption( $field['id'] ) );

        if (method_exists( $fieldClass, 'localize' )) {
            if (!isset( $localizeData[$field['type']] )) {
                $localizeData[$field['type']] = array();
            }
            $localizeData[$field['type']][$field['id']] = $theField->localize( $field );
        }

        return $localizeData;
    }

    public function addLocalizeData($localizeData)
    {

        $localizeData['required'] = $this->getRequired();
        $localizeData['required_child'] = $this->getRequiredChild();
        $localizeData['fields'] = $this->getFields();

        $localizeData['folds'] = $this->folds;

        // Make sure the children are all hidden properly.
        foreach ($this->getFields() as $key => $value) {
            if (in_array( $key, $this->fieldsHidden )) {
                foreach ($value as $k => $v) {
                    if (!in_array( $k, $this->fieldsHidden )) {
                        $this->fieldsHidden[] = $k;
                        $this->folds[$k] = "hide";
                    }
                }
            }
        }

        $localizeData['fieldsHidden'] = $this->fieldsHidden;

        return $localizeData;
    }

    /**
     * @param $fieldType
     * @return bool|string
     */
    public function getFieldClass($fieldType)
    {
        $fieldClass = "Mozart\\Forms\\Component\\Field\\Type\\" . ucfirst( Str::camel( $fieldType ) );

        if (false === class_exists( $fieldClass )) {
            if (false === class_exists( $fieldClass . 'Field' )) {
                return false;
            } else {
                $fieldClass = $fieldClass . 'Field';
            }
        }

        return $fieldClass;
    }

    /**
     * Field HTML OUTPUT.
     * Gets option from options array, then calls the specific field type class - allows extending by other devs
     *
     * @param array  $field
     * @param string $v
     *
     * @return mixed|string
     * @throws \Symfony\Component\Debug\Exception\ClassNotFoundException
     */
    public function fieldInput($field, $v = null)
    {
        $output = '';
        if (isset( $field['callback'] ) && function_exists( $field['callback'] )) {
            $output = call_user_func( $field['callback'], $field, $this->builder->getOption( $field['id'] ) );

            return $output;
        }

        if (isset( $field['type'] )) {

            // If the field is set not to display in the panel
            $display = true;
            if (isset( $_GET['page'] ) && $_GET['page'] == $this->builder->getOption( 'page_slug' )) {
                if (isset( $field['panel'] ) && $field['panel'] == false) {
                    $display = false;
                }
            }

            if (!$display) {
                return $output;
            }

            $fieldClass = $this->getFieldClass( $field['type'] );

            if (!$fieldClass) {
                return $output;
            }

            $value = $this->builder->getOption( $field['id'] );

            if ($v !== null) {
                $value = $v;
            }

            if (!isset( $field['name_suffix'] )) {
                $field['name_suffix'] = "";
            }

            try {
                $fieldObject = new $fieldClass( $this->builder, $field, $value );
            } catch ( \ErrorException $e) {
                echo  $e->getMessage() . ' - ' . $fieldClass;
            } catch ( \Exception $e ) {
                echo $e->getMessage() . ' - ' . $fieldClass;
            }

            //save the values into a unique array in case we need it for dependencies
            $this->fieldsValues[$field['id']] = ( isset( $value['url'] ) && is_array(
                    $value
                ) ) ? $value['url'] : $value;

            //create default data und class string and checks the dependencies of an object
            $class_string = '';
            $data_string = '';

            $this->checkDependencies( $field );

            if (!isset( $field['fields'] ) || empty( $field['fields'] )) {
                $output .= '<fieldset id="' . $this->builder->getParam(
                        'opt_name'
                    ) . '-' . $field['id'] . '" class="redux-field-container redux-field redux-field-init redux-container-' . $field['type'] . ' ' . $class_string . '" data-id="' . $field['id'] . '" ' . $data_string . ' data-type="' . $field['type'] . '">';
            }

            ob_start();
            $fieldObject->render();
            $output .= ob_get_contents();
            ob_end_clean();

            if (!empty( $field['desc'] )) {
                $field['description'] = $field['desc'];
            }

            $output .= ( isset( $field['description'] ) && $field['type'] != "info" && $field['type'] !== "section" && !empty( $field['description'] ) ) ? '<div class="description field-desc">' . $field['description'] . '</div>' : '';

            if (!isset( $field['fields'] ) || empty( $field['fields'] )) {
                $output .= '</fieldset>';
            }
        }

        return $output;
    }

    /**
     * Checks dependencies between objects based on the $field['required'] array
     * If the array is set it needs to have exactly 3 entries.
     * The first entry describes which field should be monitored by the current field. eg: "content"
     * The second entry describes the comparison parameter. eg: "equals, not, is_larger, is_smaller ,contains"
     * The third entry describes the value that we are comparing against.
     * Example: if the required array is set to array('content','equals','Hello World'); then the current
     * field will only be displayed if the field with id "content" has exactly the value "Hello World"
     *
     * @param array $field
     *
     * @return array $params
     */
    public function checkDependencies($field)
    {
        if (!empty( $field['required'] )) {

            if (!isset( $this->required_child[$field['id']] )) {
                $this->required_child[$field['id']] = array();
            }

            if (!isset( $this->required[$field['id']] )) {
                $this->required[$field['id']] = array();
            }

            if (is_array( $field['required'][0] )) {
                foreach ($field['required'] as $value) {
                    if (is_array( $value ) && count( $value ) == 3) {
                        $data = array();
                        $data['parent'] = $value[0];
                        $data['operation'] = $value[1];
                        $data['checkValue'] = $value[2];

                        $this->required[$data['parent']][$field['id']][] = $data;

                        if (!in_array( $data['parent'], $this->required_child[$field['id']] )) {
                            $this->required_child[$field['id']][] = $data;
                        }

                        $this->checkRequiredDependencies( $field, $data );
                    }
                }
            } else {
                $data = array();
                $data['parent'] = $field['required'][0];
                $data['operation'] = $field['required'][1];
                $data['checkValue'] = $field['required'][2];

                $this->required[$data['parent']][$field['id']][] = $data;

                if (!in_array( $data['parent'], $this->required_child[$field['id']] )) {
                    $this->required_child[$field['id']][] = $data;
                }

                $this->checkRequiredDependencies( $field, $data );
            }

        }
    }


    /**
     * Get fold values into an array suitable for setting folds
     */
    public function _fold_values()
    {

        if (!is_null( $this->builder->getSectionManager()->getSections() )) {

            foreach ($this->builder->getSectionManager()->getSections() as $section) {
                if (isset( $section['fields'] )) {
                    foreach ($section['fields'] as $field) {
                        if (isset( $field['fields'] ) && is_array( $field['fields'] )) {
                            foreach ($field['fields'] as $subfield) {
                                if (isset( $subfield['required'] )) {
                                    $this->getFold( $subfield );
                                }
                            }
                        }
                        if (isset( $field['required'] )) {
                            $this->getFold( $field );
                        }
                    }
                }
            }
        }

        $parents = array();

        foreach ($this->folds as $k => $fold) { // ParentFolds WITHOUT parents
            if (empty( $fold['children'] ) || !empty( $fold['children']['parents'] )) {
                continue;
            }

            $fold['value'] = $this->builder->getOption( $k );

            foreach ($fold['children'] as $key => $value) {
                if ($key == $fold['value']) {
                    unset( $fold['children'][$key] );
                }
            }

            if (empty( $fold['children'] )) {
                continue;
            }

            foreach ($fold['children'] as $key => $value) {
                foreach ($value as $k => $hidden) {
                    if (!in_array( $hidden, $this->toHide )) {
                        $this->toHide[] = $hidden;
                    }
                }
            }

            $parents[] = $fold;
        }

        return $this->folds;
    }

    /**
     * Get the fold values
     *
     * @param array $field
     *
     * @return array
     */
    public function getFold($field)
    {
        if (!is_array( $field['required'] )) {

            /*
                Example variable:
                    $var = array(
                    'fold' => 'id'
                    );
                */

            $this->folds[$field['required']]['children'][1][] = $field['id'];
            $this->folds[$field['id']]['parent'] = $field['required'];
        } else {
//                $parent = $foldk = $field['required'][0];
            $foldk = $field['required'][0];
//                $comparison = $field['required'][1];
            $value = $foldv = $field['required'][2];
            //foreach ($field['required'] as $foldk=>$foldv) {

            if (is_array( $value )) {
                /*
                    Example variable:
                        $var = array(
                        'fold' => array( 'id' , '=', array(1, 5) )
                        );
                    */

                foreach ($value as $foldvValue) {
                    //echo 'id: '.$field['id']." key: ".$foldk.' f-val-'.print_r($foldv)." foldvValue".$foldvValue;
                    $this->folds[$foldk]['children'][$foldvValue][] = $field['id'];
                    $this->folds[$field['id']]['parent'] = $foldk;
                }
            } else {

                if (count( $field['required'] ) === 1 && is_numeric( $foldk )) {
                    /*
                        Example variable:
                            $var = array(
                            'fold' => array( 'id' )
                            );
                        */
                    $this->folds[$field['id']]['parent'] = $foldk;
                    $this->folds[$foldk]['children'][1][] = $field['id'];
                } else {
                    /*
                        Example variable:
                            $var = array(
                            'fold' => array( 'id' => 1 )
                            );
                        */
                    if (empty( $foldv )) {
                        $foldv = 0;
                    }

                    $this->folds[$field['id']]['parent'] = $foldk;
                    $this->folds[$foldk]['children'][$foldv][] = $field['id'];
                }
            }
            //}
        }

        return $this->folds;
    }

    /**
     * @param $field
     * @param $data
     */
    public function checkRequiredDependencies($field, $data)
    {
        //required field must not be hidden. otherwise hide this one by default

        if (!in_array(
                $data['parent'],
                $this->fieldsHidden
            ) && ( !isset( $this->folds[$field['id']] ) || $this->folds[$field['id']] != "hide" )
        ) {
            if ($this->builder->getOption( $data['parent'] ) != '') {
                $return = $this->builder->compareValueDependencies(
                    $this->builder->getOption( $data['parent'] ),
                    $data['checkValue'],
                    $data['operation']
                );
            }
        }

        if (( isset( $return ) && $return ) &&
            ( !isset( $this->folds[$field['id']] ) ||
                $this->folds[$field['id']] != "hide" )
        ) {
            $this->folds[$field['id']] = "show";
        } else {
            $this->folds[$field['id']] = "hide";
            if (!in_array( $field['id'], $this->fieldsHidden )) {
                $this->fieldsHidden[] = $field['id'];
            }
        }
    }

    /**
     * Can Output CSS
     * Check if a field meets its requirements before outputting to CSS
     *
     * @param $field
     *
     * @return bool
     */
    public function canOutputCSS($field)
    {
        $return = true;

        if (isset( $field['force_output'] ) && $field['force_output'] == true) {
            return false;
        }

        /**
         * $field['required'] is an array of three values:
         * Parent field ID, comparison operator, and value which affects the field’s visibility.
         */
        if (empty( $field['required'] ) || !isset( $field['required'][0] )) {
            return false;
        }

        if (!is_array( $field['required'][0] ) &&
            count( $field['required'] ) == 3
        ) {
            $parentValue = $this->fields[$field['required'][0]];
            $checkValue = $field['required'][2];
            $operation = $field['required'][1];
            $return = $this->builder->compareValueDependencies( $parentValue, $checkValue, $operation );
        } elseif (is_array( $field['required'][0] )) {
            foreach ($field['required'] as $required) {
                if (!is_array( $required[0] ) && count( $required ) == 3) {
                    $return = $this->builder->compareValueDependencies(
                        $this->fields[$required[0]],
                        $required[2],
                        $required[1]
                    );
                }
                if (!$return) {
                    return false;
                }
            }
        }

        return $return;
    }

}
