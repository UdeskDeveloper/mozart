<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Option;


class FieldManager {

    /**
     * Field HTML OUTPUT.
     * Gets option from options array, then calls the specific field type class - allows extending by other devs
     *
     * @param array $field
     * @param string $v
     *
     * @return void
     */
    public function _field_input( $field, $v = null )
    {
        if (isset( $field['callback'] ) && function_exists( $field['callback'] )) {
            $value = ( isset( $this->options[$field['id']] ) ) ? $this->options[$field['id']] : '';
            call_user_func( $field['callback'], $field, $value );

            return;
        }

        if (isset( $field['type'] )) {

            // If the field is set not to display in the panel
            $display = true;
            if (isset( $_GET['page'] ) && $_GET['page'] == $this->params['page_slug']) {
                if (isset( $field['panel'] ) && $field['panel'] == false) {
                    $display = false;
                }
            }

            if (!$display) {
                return;
            }

            $fieldClass = $this->getFieldClass( $field['type'] );

            $value = isset( $this->options[$field['id']] ) ? $this->options[$field['id']] : '';

            if ($v !== null) {
                $value = $v;
            }

            if (!isset( $field['name_suffix'] )) {
                $field['name_suffix'] = "";
            }

            try {
                $fieldObject = new $fieldClass( $field, $value, $this );
            } catch ( \Exception $e ) {
                /** @var \Exception $e */
                throw new  ClassNotFoundException( 'No Class Found for "' . $field['type'] . '" type', $e );
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
                echo '<fieldset id="' . $this->params['opt_name'] . '-' . $field['id'] . '" class="redux-field-container redux-field redux-field-init redux-container-' . $field['type'] . ' ' . $class_string . '" data-id="' . $field['id'] . '" ' . $data_string . ' data-type="' . $field['type'] . '">';
            }

            $fieldObject->render();

            if (!empty( $field['desc'] )) {
                $field['description'] = $field['desc'];
            }

            echo ( isset( $field['description'] ) && $field['type'] != "info" && $field['type'] !== "section" && !empty( $field['description'] ) ) ? '<div class="description field-desc">' . $field['description'] . '</div>' : '';

            if (!isset( $field['fields'] ) || empty( $field['fields'] )) {
                echo '</fieldset>';
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
    public function _can_output_css( $field )
    {
        $return = true;

        if (isset( $field['force_output'] ) && $field['force_output'] == true) {
            return $return;
        }

        if (!empty( $field['required'] )) {
            if (isset( $field['required'][0] )) {
                if (!is_array( $field['required'][0] ) && count( $field['required'] ) == 3) {
                    $parentValue = $GLOBALS[$this->params['global_variable']][$field['required'][0]];
                    $checkValue = $field['required'][2];
                    $operation = $field['required'][1];
                    $return = $this->compareValueDependencies( $parentValue, $checkValue, $operation );
                } elseif (is_array( $field['required'][0] )) {
                    foreach ($field['required'] as $required) {
                        if (!is_array( $required[0] ) && count( $required ) == 3) {
                            $parentValue = $GLOBALS[$this->params['global_variable']][$required[0]];
                            $checkValue = $required[2];
                            $operation = $required[1];
                            $return = $this->compareValueDependencies( $parentValue, $checkValue, $operation );
                        }
                        if (!$return) {
                            return $return;
                        }
                    }
                }
            }
        }

        return $return;
    }

} 