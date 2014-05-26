<?php

namespace Mozart\RealEstate\PropertyBundle\Twig;

class PropertyExtension extends \Twig_Extension
{

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('action_links', array(
                $this,
                'get_link_actions'))
        );
    }

    public function getName()
    {
        return 'realestate_property';
    }

    public function get_link_actions($post_id)
    {
        $actions = array();
        $can_edit_property = current_user_can('edit_real_estate_properties', $post_id);

        $post = get_post($post_id);

        $actions['edit'] = array(
            'href' => set_url_scheme(add_query_arg('action', 'edit', get_permalink($post_id))),
            'title' => sprintf(__('Edit %s'), $post->title),
            'content' => __('Edit'),
            'icon' => 'mozicon-pencil'
        );

        if (in_array($post->post_status, array('pending', 'draft', 'future'))) {
            if ($can_edit_property) {
                $actions['view'] = array(
                    'href' => set_url_scheme(add_query_arg('preview', 'true', get_permalink($post_id))),
                    'title' => sprintf(__('Preview %s'), $post->title),
                    'content' => __('Preview'),
                    'icon' => 'mozicon-eye'
                );
            }
        } elseif ('trash' != $post->post_status) {
            $actions['view'] = array(
                'href' => get_permalink($post_id),
                'title' => sprintf(__('View %s'), $post->title),
                'content' => __('View'),
                'icon' => 'mozicon-eye'
            );
        }

        if ('pending' == $post->post_status) {
            $actions['draft'] = array(
                'href' => set_url_scheme(wp_nonce_url(add_query_arg('action', 'draft', get_permalink($post_id)), 'draft-post_' . $post_id)),
                'title' => esc_attr(sprintf(__('Cancel Pending and Save as Draft %s'), $post->title)),
                'content' => __('Cancel Pending'),
                'icon' => 'mozicon-download2'
            );
        } elseif ('publish' == $post->post_status) {
            $actions['unpublish'] = array(
                'href' => set_url_scheme(wp_nonce_url(add_query_arg('action', 'unpublish', get_permalink($post_id)), 'unpublish-post_' . $post_id)),
                'title' => esc_attr(sprintf(__('Unpublish %s'), $post->title)),
                'content' => __('Unpublish'),
                'icon' => 'mozicon-download2'
            );
        } elseif ('draft' == $post->post_status) {
            $actions['pending'] = array(
                'href' => set_url_scheme(wp_nonce_url(add_query_arg('action', 'pending', get_permalink($post_id)), 'pending-post_' . $post_id)),
                'title' => esc_attr(sprintf(__('Submit for Review %s'), $post->title)),
                'content' => __('Submit for Review'),
                'icon' => 'mozicon-upload2'
            );
        }

        if (current_user_can('delete_published_real_estate_properties', $post_id)) {
            if ('trash' == $post->post_status) {
                $actions['untrash'] = array(
                    'href' => set_url_scheme(wp_nonce_url(add_query_arg('action', 'publish', get_permalink($post_id)), 'publish-post_' . $post_id)),
                    'title' => esc_attr(__('Restore this item from the Trash')),
                    'content' => __('Restore'),
                    'icon' => 'mozicon-undo2'
                );
            } elseif (EMPTY_TRASH_DAYS) {
                $actions['trash'] = array(
                    'href' => set_url_scheme(wp_nonce_url(add_query_arg('action', 'trash', get_permalink($post_id)), 'trash-post_' . $post_id)),
                    'title' => esc_attr(__('Move this item to the Trash')),
                    'content' => __('Trash'),
                    'icon' => 'mozicon-remove'
                );
            }
            if ('trash' == $post->post_status || !EMPTY_TRASH_DAYS) {
                $actions['delete'] = array(
                    'href' => set_url_scheme(wp_nonce_url(add_query_arg('action', 'delete', get_permalink($post_id)), 'delete-post_' . $post_id)),
                    'title' => esc_attr(__('Delete this item permanently')),
                    'content' => __('Delete Permanently'),
                    'icon' => 'mozicon-remove'
                );
            }
        }


        return $actions;
    }

}
