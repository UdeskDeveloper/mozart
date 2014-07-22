<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Media extends Field
{

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
            'id'        => '',
            'url'       => '',
            'width'     => '',
            'height'    => '',
            'thumbnail' => '',
        );

        $this->value = wp_parse_args( $this->value, $defaults );

        if (!isset( $this->field['mode'] )) {
            $this->field['mode'] = "image";
        }

        if (empty( $this->value ) && !empty( $this->field['default'] )) { // If there are standard values and value is empty
            if (is_array( $this->field['default'] )) {
                if (!empty( $this->field['default']['id'] )) {
                    $this->value['id'] = $this->field['default']['id'];
                }

                if (!empty( $this->field['default']['url'] )) {
                    $this->value['url'] = $this->field['default']['url'];
                }
            } else {
                if (is_numeric( $this->field['default'] )) { // Check if it's an attachment ID
                    $this->value['id'] = $this->field['default'];
                } else { // Must be a URL
                    $this->value['url'] = $this->field['default'];
                }
            }
        }

        if (empty( $this->value['url'] ) && !empty( $this->value['id'] )) {
            $img = wp_get_attachment_image_src( $this->value['id'], 'full' );
            $this->value['url'] = $img[0];
            $this->value['width'] = $img[1];
            $this->value['height'] = $img[2];
        }

        $hide = 'hide ';

        if (( isset( $this->field['preview'] ) && $this->field['preview'] === false )) {
            $this->field['class'] .= " noPreview";
        }

        if (( !empty( $this->field['url'] ) && $this->field['url'] === true ) || isset( $this->field['preview'] ) && $this->field['preview'] === false) {
            $hide = '';
        }

        $placeholder = isset( $this->field['placeholder'] ) ? $this->field['placeholder'] : __(
            'No media selected',
            'mozart-options'
        );

        $readOnly = ' readonly="readonly"';
        if (isset( $this->field['readonly'] ) && $this->field['readonly'] === false) {
            $readOnly = '';
        }

        echo '<input placeholder="' . $placeholder . '" type="text" class="' . $hide . 'upload regular-text ' . $this->field['class'] . '" name="' . $this->field['name'] . '[url]' . $this->field['name_suffix'] . '" id="' . $this->builder->getParam('opt_name') . '[' . $this->field['id'] . '][url]" value="' . $this->value['url'] . '"' . $readOnly . '/>';
        echo '<input type="hidden" class="upload-id ' . $this->field['class'] . '" name="' . $this->field['name'] . '[id]' . $this->field['name_suffix'] . '" id="' . $this->builder->getParam('opt_name') . '[' . $this->field['id'] . '][id]" value="' . $this->value['id'] . '" />';
        echo '<input type="hidden" class="upload-height" name="' . $this->field['name'] . '[height]' . $this->field['name_suffix'] . '" id="' . $this->builder->getParam('opt_name') . '[' . $this->field['id'] . '][height]" value="' . $this->value['height'] . '" />';
        echo '<input type="hidden" class="upload-width" name="' . $this->field['name'] . '[width]' . $this->field['name_suffix'] . '" id="' . $this->builder->getParam('opt_name') . '[' . $this->field['id'] . '][width]" value="' . $this->value['width'] . '" />';
        echo '<input type="hidden" class="upload-thumbnail" name="' . $this->field['name'] . '[thumbnail]' . $this->field['name_suffix'] . '" id="' . $this->builder->getParam('opt_name') . '[' . $this->field['id'] . '][thumbnail]" value="' . $this->value['thumbnail'] . '" />';

        //Preview
        $hide = '';

        if (( isset( $this->field['preview'] ) && $this->field['preview'] === false ) || empty( $this->value['url'] )) {
            $hide = 'hide ';
        }

        if (empty( $this->value['thumbnail'] ) && !empty( $this->value['url'] )) { // Just in case
            if (!empty( $this->value['id'] )) {
                $image = wp_get_attachment_image_src(
                    $this->value['id'],
                    array(
                        150,
                        150
                    )
                );
                $this->value['thumbnail'] = $image[0];
            } else {
                $this->value['thumbnail'] = $this->value['url'];
            }
        }

        echo '<div class="' . $hide . 'screenshot">';
        echo '<a class="of-uploaded-image" href="' . $this->value['url'] . '" target="_blank">';
        echo '<img class="redux-option-image" id="image_' . $this->field['id'] . '" src="' . $this->value['thumbnail'] . '" alt="" target="_blank" rel="external" />';
        echo '</a>';
        echo '</div>';

        //Upload controls DIV
        echo '<div class="upload_button_div">';

        //If the user has WP3.5+ show upload/remove button
        echo '<span class="button media_upload_button" id="' . $this->field['id'] . '-media">' . __(
                'Upload',
                'mozart-options'
            ) . '</span>';

        $hide = '';
        if (empty( $this->value['url'] ) || $this->value['url'] == '') {
            $hide = ' hide';
        }

        echo '<span class="button remove-image' . $hide . '" id="reset_' . $this->field['id'] . '" rel="' . $this->field['id'] . '">' . __(
                'Remove',
                'mozart-options'
            ) . '</span>';

        echo '</div>';
    }

    /**
     * Functions to pass data from the PHP to the JS at render time.
     *
     * @return array Params to be saved as a javascript object accessable to the UI.
     *
     */
    public function localize($field, $value = "")
    {
        $params = array();

        if (!isset( $field['mode'] )) {
            $field['mode'] = "image";
        }

        $params['mode'] = $field['mode'];

        if (empty( $value ) && isset( $this->value )) {
            $value = $this->value;
        }

        $params['val'] = $value;

        return $params;
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
            'redux-field-media-js',
            \Mozart::parameter( 'wp.plugin.uri' ) . '/mozart/public/bundles/mozart/option/js/media/media.js',
            array( 'jquery', 'redux-js' ),
            time(),
            true
        );

        wp_enqueue_style(
            'redux-field-media-css',
            \Mozart::parameter( 'wp.plugin.uri' ) . '/mozart/public/bundles/mozart/option/fields/media/field_media.css',
            time(),
            true
        );
    }
}
