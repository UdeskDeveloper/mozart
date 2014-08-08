<?php
namespace Mozart\Component\Form\Field;

use Mozart\Component\Form\Field;

class Slides extends Field
{
    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @return void
     */
    public function render()
    {
        $defaults = array(
            'show'          => array(
                'title'       => true,
                'description' => true,
                'url'         => true,
            ),
            'content_title' => __( 'Slide', 'mozart-options' )
        );

        $this->field = wp_parse_args( $this->field, $defaults );

        echo '<div class="redux-slides-accordion" data-new-content-title="' . esc_attr(
                sprintf( __( 'New %s', 'mozart-options' ), $this->field['content_title'] )
            ) . '">';

        $x = 0;

        $multi = ( isset ( $this->field['multi'] ) && $this->field['multi'] ) ? ' multiple="multiple"' : "";

        if (isset ( $this->value ) && is_array( $this->value ) && !empty ( $this->value )) {

            $slides = $this->value;

            foreach ($slides as $slide) {

                if (empty ( $slide )) {
                    continue;
                }

                $defaults = array(
                    'title'         => '',
                    'description'   => '',
                    'sort'          => '',
                    'url'           => '',
                    'image'         => '',
                    'thumb'         => '',
                    'attachment_id' => '',
                    'height'        => '',
                    'width'         => '',
                    'select'        => array(),
                );
                $slide = wp_parse_args( $slide, $defaults );

                if (empty ( $slide['thumb'] ) && !empty ( $slide['attachment_id'] )) {
                    $img = wp_get_attachment_image_src( $slide['attachment_id'], 'full' );
                    $slide['image'] = $img[0];
                    $slide['width'] = $img[1];
                    $slide['height'] = $img[2];
                }

                echo '<div class="redux-slides-accordion-group"><fieldset class="redux-field" data-id="' . $this->field['id'] . '"><h3><span class="redux-slides-header">' . $slide['title'] . '</span></h3><div>';

                $hide = '';
                if (empty ( $slide['image'] )) {
                    $hide = ' hide';
                }

                echo '<div class="screenshot' . $hide . '">';
                echo '<a class="of-uploaded-image" href="' . $slide['image'] . '">';
                echo '<img class="redux-slides-image" id="image_image_id_' . $x . '" src="' . $slide['thumb'] . '" alt="" target="_blank" rel="external" />';
                echo '</a>';
                echo '</div>';

                echo '<div class="redux_slides_add_remove">';

                echo '<span class="button media_upload_button" id="add_' . $x . '">' . __(
                        'Upload',
                        'mozart-options'
                    ) . '</span>';

                $hide = '';
                if (empty ( $slide['image'] ) || $slide['image'] == '') {
                    $hide = ' hide';
                }

                echo '<span class="button remove-image' . $hide . '" id="reset_' . $x . '" rel="' . $slide['attachment_id'] . '">' . __(
                        'Remove',
                        'mozart-options'
                    ) . '</span>';

                echo '</div>' . "\n";

                echo '<ul id="' . $this->field['id'] . '-ul" class="redux-slides-list">';

                if ($this->field['show']['title']) {
                    $title_type = "text";
                } else {
                    $title_type = "hidden";
                }

                $placeholder = ( isset ( $this->field['placeholder']['title'] ) ) ? esc_attr(
                    $this->field['placeholder']['title']
                ) : __( 'Title', 'mozart-options' );
                echo '<li><input type="' . $title_type . '" id="' . $this->field['id'] . '-title_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][title]' . $this->field['name_suffix'] . '" value="' . esc_attr(
                        $slide['title']
                    ) . '" placeholder="' . $placeholder . '" class="full-text slide-title" /></li>';

                if ($this->field['show']['description']) {
                    $placeholder = ( isset ( $this->field['placeholder']['description'] ) ) ? esc_attr(
                        $this->field['placeholder']['description']
                    ) : __( 'Description', 'mozart-options' );
                    echo '<li><textarea name="' . $this->field['name'] . '[' . $x . '][description]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-description_' . $x . '" placeholder="' . $placeholder . '" class="large-text" rows="6">' . esc_attr(
                            $slide['description']
                        ) . '</textarea></li>';
                }

                $placeholder = ( isset ( $this->field['placeholder']['url'] ) ) ? esc_attr(
                    $this->field['placeholder']['url']
                ) : __( 'URL', 'mozart-options' );
                if ($this->field['show']['url']) {
                    $url_type = "text";
                } else {
                    $url_type = "hidden";
                }

                echo '<li><input type="' . $url_type . '" id="' . $this->field['id'] . '-url_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][url]' . $this->field['name_suffix'] . '" value="' . esc_attr(
                        $slide['url']
                    ) . '" class="full-text" placeholder="' . $placeholder . '" /></li>';
                echo '<li><input type="hidden" class="slide-sort" name="' . $this->field['name'] . '[' . $x . '][sort]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-sort_' . $x . '" value="' . $slide['sort'] . '" />';
                echo '<li><input type="hidden" class="upload-id" name="' . $this->field['name'] . '[' . $x . '][attachment_id]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-image_id_' . $x . '" value="' . $slide['attachment_id'] . '" />';
                echo '<input type="hidden" class="upload-thumbnail" name="' . $this->field['name'] . '[' . $x . '][thumb]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-thumb_url_' . $x . '" value="' . $slide['thumb'] . '" readonly="readonly" />';
                echo '<input type="hidden" class="upload" name="' . $this->field['name'] . '[' . $x . '][image]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-image_url_' . $x . '" value="' . $slide['image'] . '" readonly="readonly" />';
                echo '<input type="hidden" class="upload-height" name="' . $this->field['name'] . '[' . $x . '][height]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-image_height_' . $x . '" value="' . $slide['height'] . '" />';
                echo '<input type="hidden" class="upload-width" name="' . $this->field['name'] . '[' . $x . '][width]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-image_width_' . $x . '" value="' . $slide['width'] . '" /></li>';
                echo '<li><a href="javascript:void(0);" class="button deletion redux-slides-remove">' . sprintf(
                        __( 'Delete %s', 'mozart-options' ),
                        $this->field['content_title']
                    ) . '</a></li>';
                echo '</ul></div></fieldset></div>';
                $x++;
            }
        }

        if ($x == 0) {
            echo '<div class="redux-slides-accordion-group"><fieldset class="redux-field" data-id="' . $this->field['id'] . '"><h3><span class="redux-slides-header">New ' . $this->field['content_title'] . '</span></h3><div>';

            $hide = ' hide';

            echo '<div class="screenshot' . $hide . '">';
            echo '<a class="of-uploaded-image" href="">';
            echo '<img class="redux-slides-image" id="image_image_id_' . $x . '" src="" alt="" target="_blank" rel="external" />';
            echo '</a>';
            echo '</div>';

            //Upload controls DIV
            echo '<div class="upload_button_div">';

            //If the user has WP3.5+ show upload/remove button
            echo '<span class="button media_upload_button" id="add_' . $x . '">' . __(
                    'Upload',
                    'mozart-options'
                ) . '</span>';

            echo '<span class="button remove-image' . $hide . '" id="reset_' . $x . '" rel="' . $this->builder->getParam('opt_name') . '[' . $this->field['id'] . '][attachment_id]">' . __(
                    'Remove',
                    'mozart-options'
                ) . '</span>';

            echo '</div>' . "\n";

            echo '<ul id="' . $this->field['id'] . '-ul" class="redux-slides-list">';
            if ($this->field['show']['title']) {
                $title_type = "text";
            } else {
                $title_type = "hidden";
            }
            $placeholder = ( isset ( $this->field['placeholder']['title'] ) ) ? esc_attr(
                $this->field['placeholder']['title']
            ) : __( 'Title', 'mozart-options' );
            echo '<li><input type="' . $title_type . '" id="' . $this->field['id'] . '-title_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][title]' . $this->field['name_suffix'] . '" value="" placeholder="' . $placeholder . '" class="full-text slide-title" /></li>';

            if ($this->field['show']['description']) {
                $placeholder = ( isset ( $this->field['placeholder']['description'] ) ) ? esc_attr(
                    $this->field['placeholder']['description']
                ) : __( 'Description', 'mozart-options' );
                echo '<li><textarea name="' . $this->field['name'] . '[' . $x . '][description]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-description_' . $x . '" placeholder="' . $placeholder . '" class="large-text" rows="6"></textarea></li>';
            }
            $placeholder = ( isset ( $this->field['placeholder']['url'] ) ) ? esc_attr(
                $this->field['placeholder']['url']
            ) : __( 'URL', 'mozart-options' );
            if ($this->field['show']['url']) {
                $url_type = "text";
            } else {
                $url_type = "hidden";
            }
            echo '<li><input type="' . $url_type . '" id="' . $this->field['id'] . '-url_' . $x . '" name="' . $this->field['name'] . '[' . $x . '][url]' . $this->field['name_suffix'] . '" value="" class="full-text" placeholder="' . $placeholder . '" /></li>';
            echo '<li><input type="hidden" class="slide-sort" name="' . $this->field['name'] . '[' . $x . '][sort]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-sort_' . $x . '" value="' . $x . '" />';
            echo '<li><input type="hidden" class="upload-id" name="' . $this->field['name'] . '[' . $x . '][attachment_id]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-image_id_' . $x . '" value="" />';
            echo '<input type="hidden" class="upload" name="' . $this->field['name'] . '[' . $x . '][image]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-image_url_' . $x . '" value="" readonly="readonly" />';
            echo '<input type="hidden" class="upload-height" name="' . $this->field['name'] . '[' . $x . '][height]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-image_height_' . $x . '" value="" />';
            echo '<input type="hidden" class="upload-width" name="' . $this->field['name'] . '[' . $x . '][width]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-image_width_' . $x . '" value="" /></li>';
            echo '<input type="hidden" class="upload-thumbnail" name="' . $this->field['name'] . '[' . $x . '][thumb]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-thumb_url_' . $x . '" value="" /></li>';
            echo '<li><a href="javascript:void(0);" class="button deletion redux-slides-remove">' . sprintf(
                    __( 'Delete %s', 'mozart-options' ),
                    $this->field['content_title']
                ) . '</a></li>';
            echo '</ul></div></fieldset></div>';
        }
        echo '</div><a href="javascript:void(0);" class="button redux-slides-add button-primary" rel-id="' . $this->field['id'] . '-ul" rel-name="' . $this->field['name'] . '[title][]' . $this->field['name_suffix'] . '">' . sprintf(
                __( 'Add %s', 'mozart-options' ),
                $this->field['content_title']
            ) . '</a><br/>';
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     *
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
            \Mozart::parameter( 'wp.plugin.uri' ) . '/mozart/public/bundles/mozart/form/fields/media/field_media.css',
            time(),
            true
        );

        wp_enqueue_script(
            'redux-field-slides-js',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/form/fields/slides/field_slides.js',
            array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion', 'wp-color-picker' ),
            time(),
            true
        );

        wp_enqueue_style(
            'redux-field-slides-css',
            \Mozart::parameter(
                'wp.plugin.uri'
            ) . '/mozart/public/bundles/mozart/form/fields/slides/field_slides.css',
            time(),
            true
        );
    }
}
