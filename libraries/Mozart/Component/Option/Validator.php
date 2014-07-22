<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Option;


class Validator {

    /**
     * Validate values from options form (used in settings api validate function)
     * calls the custom validation class for the field so authors can override with custom classes
     *
     * @param array $plugin_options
     * @param array $options
     * @param $sections
     *
     * @return array $plugin_options
     */
    public function _validate_values( $plugin_options, $options, $sections )
    {
        foreach ($sections as $k => $section) {
            if (!isset( $section['fields'] )) {
                continue;
            }
            foreach ($section['fields'] as $fkey => $field) {
                $field['section_id'] = $k;

                if (isset( $field['type'] ) &&
                    ( $field['type'] == 'checkbox' ||
                        $field['type'] == 'checkbox_hide_below' ||
                        $field['type'] == 'checkbox_hide_all' ) &&
                    !isset( $plugin_options[$field['id']] )
                ) {
                    $plugin_options[$field['id']] = 0;
                }

                // Default 'not_empty 'flag to false.
                $isNotEmpty = false;

                // Make sure 'validate' field is set.
                if (isset( $field['validate'] )) {

                    // Make sure 'validate field' is set to 'not_empty' or 'email_not_empty'
                    if ($field['validate'] == 'not_empty' ||
                        $field['validate'] == 'email_not_empty' ||
                        $field['validate'] == 'numeric_not_empty'
                    ) {

                        // Set the flag.
                        $isNotEmpty = true;
                    }
                }

                // Check for empty id value
                if (!isset( $plugin_options[$field['id']] ) || $plugin_options[$field['id']] == '') {

                    // If we are looking for an empty value, in the case of 'not_empty'
                    // then we need to keep processing.
                    if (!$isNotEmpty) {

                        // Empty id and not checking for 'not_empty.  Bail out...
                        continue;
                    }
                }

                // Force validate of custom field types
                if (isset( $field['type'] ) && !isset( $field['validate'] )) {
                    if ($field['type'] == 'color' || $field['type'] == 'color_gradient') {
                        $field['validate'] = 'color';
                    } elseif ($field['type'] == 'date') {
                        $field['validate'] = 'date';
                    }
                }

                if (isset( $field['validate'] )) {
                    $validateClass = 'Mozart\\Component\\Form\\Validation\\' . Str::camel( $field['validate'] );

                    if (class_exists( $validateClass )) {

                        if (empty ( $options[$field['id']] )) {
                            $options[$field['id']] = '';
                        }

                        if (isset( $plugin_options[$field['id']] ) && is_array(
                                $plugin_options[$field['id']]
                            ) && !empty( $plugin_options[$field['id']] )
                        ) {
                            foreach ($plugin_options[$field['id']] as $key => $value) {
                                $before = $after = null;
                                if (isset( $plugin_options[$field['id']][$key] ) && !empty( $plugin_options[$field['id']][$key] )) {
                                    if (is_array( $plugin_options[$field['id']][$key] )) {
                                        $before = $plugin_options[$field['id']][$key];
                                    } else {
                                        $before = trim( $plugin_options[$field['id']][$key] );
                                    }
                                }

                                if (isset( $options[$field['id']][$key] ) && !empty( $options[$field['id']][$key] )) {
                                    $after = $options[$field['id']][$key];
                                }

                                $validation = new $validateClass( $this, $field, $before, $after );
                                if (!empty( $validation->value )) {
                                    $plugin_options[$field['id']][$key] = $validation->value;
                                } else {
                                    unset( $plugin_options[$field['id']][$key] );
                                }

                                if (isset( $validation->error )) {
                                    $this->errors[] = $validation->error;
                                }

                                if (isset( $validation->warning )) {
                                    $this->warnings[] = $validation->warning;
                                }
                            }
                        } else {
                            if (is_array( $plugin_options[$field['id']] )) {
                                $pofi = $plugin_options[$field['id']];
                            } else {
                                $pofi = trim( $plugin_options[$field['id']] );
                            }

                            $validation = new $validateClass( $this, $field, $pofi, $options[$field['id']] );
                            $plugin_options[$field['id']] = $validation->value;

                            if (isset( $validation->error )) {
                                $this->errors[] = $validation->error;
                            }

                            if (isset( $validation->warning )) {
                                $this->warnings[] = $validation->warning;
                            }
                        }

                        continue;
                    }
                }

                if (isset( $field['validate_callback'] ) && function_exists( $field['validate_callback'] )) {
                    $callbackvalues = call_user_func(
                        $field['validate_callback'],
                        $field,
                        $plugin_options[$field['id']],
                        $options[$field['id']]
                    );
                    $plugin_options[$field['id']] = $callbackvalues['value'];

                    if (isset( $callbackvalues['error'] )) {
                        $this->errors[] = $callbackvalues['error'];
                    }

                    if (isset( $callbackvalues['warning'] )) {
                        $this->warnings[] = $callbackvalues['warning'];
                    }
                }
            }
        }

        return $plugin_options;
    }
} 