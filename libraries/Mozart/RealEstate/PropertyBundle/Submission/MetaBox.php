<?php

namespace Mozart\RealEstate\PropertyBundle\Submission;

class MetaBox extends \wpalchemy_MetaBox
{
    public function force_save($post_id)
    {
        /**
         * note: the "save_post" action fires for saving revisions and post/pages,
         * when saving a post this function fires twice, once for a revision save,
         * and again for the post/page save ... the $post_id is different for the
         * revision save, this means that "get_post_meta()" will not work if trying
         * to get values for a revision (as it has no post meta data)
         * see http://alexking.org/blog/2008/09/06/wordpress-26x-duplicate-custom-field-issue
         *
         * why let the code run twice? wordpress does not currently save post meta
         * data per revisions (I think it should, so users can do a complete revert),
         * so in the case that this functionality changes, let it run twice
         */
        $real_post_id = isset($_POST['post_ID']) ? $_POST['post_ID'] : NULL;

//        // check autosave
//        if (defined('DOING_AUTOSAVE') AND DOING_AUTOSAVE AND !$this->autosave) return $post_id;
//
//        // make sure data came from our meta box, verify nonce
//        $nonce = isset($_POST[$this->id.'_nonce']) ? $_POST[$this->id.'_nonce'] : NULL ;
//        if (!wp_verify_nonce($nonce, $this->id)) return $post_id;
//
//        // check user permissions
//        if ($_POST['post_type'] == 'page')
//        {
//            if (!current_user_can('edit_page', $post_id)) return $post_id;
//        }
//        else
//        {
//            if (!current_user_can('edit_post', $post_id)) return $post_id;
//        }
// authentication passed, save data

        $new_data = isset($_POST[$this->id]) ? $_POST[$this->id] : NULL;

        \wpalchemy_MetaBox::clean($new_data);

        if (empty($new_data)) {
            $new_data = NULL;
        }

// filter: save
        if ($this->has_filter('save')) {
            $new_data = $this->apply_filters('save', $new_data, $real_post_id);

            /**
             * halt saving
             * @since 1.3.4
             */
            if (FALSE === $new_data)
                return $post_id;

            \wpalchemy_MetaBox::clean($new_data);
        }

// get current fields, use $real_post_id (checked for in both modes)
        $current_fields = get_post_meta($real_post_id, $this->id . '_fields', true);

        if ($this->mode == WPALCHEMY_MODE_EXTRACT) {
            $new_fields = array();

            if (is_array($new_data)) {
                foreach ($new_data as $k => $v) {
                    $field = $this->prefix . $k;

                    array_push($new_fields, $field);

                    $new_value = $new_data[$k];

                    if (is_null($new_value)) {
                        delete_post_meta($post_id, $field);
                    } else {
                        update_post_meta($post_id, $field, $new_value);
                    }
                }
            }

            $diff_fields = array_diff((array) $current_fields, $new_fields);

            if (is_array($diff_fields)) {
                foreach ($diff_fields as $field) {
                    delete_post_meta($post_id, $field);
                }
            }

            delete_post_meta($post_id, $this->id . '_fields');

            if (!empty($new_fields)) {
                add_post_meta($post_id, $this->id . '_fields', $new_fields, true);
            }

// keep data tidy, delete values if previously using WPALCHEMY_MODE_ARRAY
            delete_post_meta($post_id, $this->id);
        } else {
            if (is_null($new_data)) {
                delete_post_meta($post_id, $this->id);
            } else {
                update_post_meta($post_id, $this->id, $new_data);
            }

// keep data tidy, delete values if previously using WPALCHEMY_MODE_EXTRACT
            if (is_array($current_fields)) {
                foreach ($current_fields as $field) {
                    delete_post_meta($post_id, $field);
                }

                delete_post_meta($post_id, $this->id . '_fields');
            }
        }

// action: save
        if ($this->has_action('save')) {
            $this->do_action('save', $new_data, $real_post_id);
        }

        return $post_id;
    }

}
