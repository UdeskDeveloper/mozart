<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Sorter extends Field
{
    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     */
    public function render()
    {
        if (!is_array( $this->value ) && isset( $this->field['options'] )) {
            $this->value = $this->field['options'];
        }

        if (!isset( $this->field['args'] )) {
            $this->field['args'] = array();
        }

        if (isset( $this->field['data'] ) && !empty( $this->field['data'] ) && is_array( $this->field['data'] )) {
            foreach ($this->field['data'] as $key => $data) {
                if (!isset( $this->field['args'][$key] )) {
                    $this->field['args'][$key] = array();
                }
                $this->field['options'][$key] = $this->builder->get_wordpress_data( $data, $this->field['args'][$key] );
            }
        }

        // Make sure to get list of all the default blocks first
        $all_blocks = !empty( $this->field['options'] ) ? $this->field['options'] : array();
        $temp = array(); // holds default blocks
        $temp2 = array(); // holds saved blocks

        foreach ($all_blocks as $blocks) {
            $temp = array_merge( $temp, $blocks );
        }

        $sortlists = $this->value;

        if (is_array( $sortlists )) {
            foreach ($sortlists as $sortlist) {
                $temp2 = array_merge( $temp2, $sortlist );
            }

            // now let's compare if we have anything missing
            foreach ($temp as $k => $v) {
                if (!array_key_exists( $k, $temp2 )) {
                    $sortlists['disabled'][$k] = $v;
                }
            }

            // now check if saved blocks has blocks not registered under default blocks
            foreach ($sortlists as $key => $sortlist) {
                foreach ($sortlist as $k => $v) {
                    if (!array_key_exists( $k, $temp )) {
                        unset( $sortlist[$k] );
                    }
                }
                $sortlists[$key] = $sortlist;
            }

            // assuming all sync'ed, now get the correct naming for each block
            foreach ($sortlists as $key => $sortlist) {
                foreach ($sortlist as $k => $v) {
                    $sortlist[$k] = $temp[$k];
                }
                $sortlists[$key] = $sortlist;
            }

            if ($sortlists) {
                echo '<fieldset id="' . $this->field['id'] . '" class="redux-sorter-container redux-sorter">';

                foreach ($sortlists as $group => $sortlist) {
                    $filled = "";

                    if (isset( $this->field['limits'][$group] ) && count(
                            $sortlist
                        ) >= $this->field['limits'][$group]
                    ) {
                        $filled = " filled";
                    }

                    echo '<ul id="' . $this->field['id'] . '_' . $group . '" class="sortlist_' . $this->field['id'] . $filled . '" data-id="' . $this->field['id'] . '" data-group-id="' . $group . '">';
                    echo '<h3>' . $group . '</h3>';

                    if (!isset( $sortlist['placebo'] )) {
                        array_unshift( $sortlist, array( "placebo" => "placebo" ) );
                    }

                    foreach ($sortlist as $key => $list) {

                        echo '<input class="sorter-placebo" type="hidden" name="' . $this->field['name'] . '[' . $group . '][placebo]' . $this->field['name_suffix'] . '" value="placebo">';

                        if ($key != "placebo") {

                            echo '<li id="' . $key . '" class="sortee">';
                            echo '<input class="position ' . $this->field['class'] . '" type="hidden" name="' . $this->field['name'] . '[' . $group . '][' . $key . ']' . $this->field['name_suffix'] . '" value="' . $list . '">';
                            echo $list;
                            echo '</li>';
                        }
                    }

                    echo '</ul>';
                }
                echo '</fieldset>';
            }
        }
    }

    public function enqueue()
    {
        wp_enqueue_style(
            'redux-field-sorder-css',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/sorter/field_sorter.css',
            time(),
            true
        );

        wp_enqueue_script(
            'redux-field-sorter-js',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/sorter/field_sorter.js',
            array( 'jquery', 'redux-js' ),
            time(),
            true
        );
    }

    /**
     * Functions to pass data from the PHP to the JS at render time.
     *
     * @return array Params to be saved as a javascript object accessable to the UI.
     */
    public function localize($field, $value = "")
    {
        $params = array();

        if (isset( $field['limits'] ) && !empty( $field['limits'] )) {
            $params['limits'] = $field['limits'];
        }

        if (empty( $value )) {
            $value = $this->value;
        }
        $params['val'] = $value;

        return $params;
    }
}
