<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Gallery extends Field
{
    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @return void
     */
    public function render()
    {
        echo '<div class="screenshot">';

        if (!empty( $this->value )) {
            $ids = explode( ',', $this->value );

            foreach ($ids as $attachment_id) {
                $img = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
                echo '<a class="of-uploaded-image" href="' . $img[0] . '">';
                echo '<img class="redux-option-image" id="image_' . $this->field['id'] . '_' . $attachment_id . '" src="' . $img[0] . '" alt="" target="_blank" rel="external" />';
                echo '</a>';
            }
        }

        echo '</div>';
        echo '<a href="#" onclick="return false;" id="edit-gallery" class="gallery-attachments button button-primary">' . __(
                'Add/Edit Gallery',
                'mozart-options'
            ) . '</a> ';
        echo '<a href="#" onclick="return false;" id="clear-gallery" class="gallery-attachments button">' . __(
                'Clear Gallery',
                'mozart-options'
            ) . '</a>';
        echo '<input type="hidden" class="gallery_values ' . $this->field['class'] . '" value="' . esc_attr(
                $this->value
            ) . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '" />';
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     * @return void
     */
    public function enqueue()
    {
        if (function_exists( 'wp_enqueue_media' )) {
            wp_enqueue_media();
        } else {
            wp_enqueue_script( 'media-upload' );
            wp_enqueue_script( 'thickbox' );
            wp_enqueue_style( 'thickbox' );
        }

        wp_enqueue_script(
            'redux-field-gallery-js',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/option/fields/gallery/field_gallery.js',
            array( 'jquery', 'redux-js' ),
            time(),
            true
        );
    }
}
