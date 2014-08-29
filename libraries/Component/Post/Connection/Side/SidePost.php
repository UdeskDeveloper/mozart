<?php
namespace Mozart\Component\Post\Connection\Side;

use Mozart\Component\Post\Connection\Exception\Exception;
use Mozart\Component\Post\Connection\ItemList\ItemList;

class SidePost extends Side
{
    protected $item_type = 'ItemPost';

    public function __construct($query_vars)
    {
        $this->query_vars = $query_vars;
    }

    public function get_object_type()
    {
        return 'post';
    }

    public function first_post_type()
    {
        return $this->query_vars['post_type'][0];
    }

    private function get_ptype()
    {
        $ptype = $this->first_post_type();

        $ptype_object = get_post_type_object( $ptype );

        if (!$ptype_object) {
            throw new Exception( "Can't find $ptype." );
        }

        return $ptype_object;
    }

    public function get_base_qv($q)
    {
        if (isset( $q['post_type'] ) && 'any' != $q['post_type']) {
            $common = array_intersect( $this->query_vars['post_type'], (array) $q['post_type'] );

            if (!$common) {
                unset( $q['post_type'] );
            }
        }

        return array_merge(
            $this->query_vars,
            $q,
            array(
                'suppress_filters'    => false,
                'ignore_sticky_posts' => true,
            )
        );
    }

    public function get_desc()
    {
        return implode( ', ', array_map( array( $this, 'post_type_label' ), $this->query_vars['post_type'] ) );
    }

    private function post_type_label($post_type)
    {
        $cpt = get_post_type_object( $post_type );

        return $cpt ? $cpt->label : $post_type;
    }

    public function get_title()
    {
        return $this->get_labels()->name;
    }

    public function get_labels()
    {
        try {
            $labels = $this->get_ptype()->labels;
        } catch ( Exception $e ) {
            trigger_error( $e->getMessage(), E_USER_WARNING );
            $labels = new \stdClass;
        }

        return $labels;
    }

    public function can_edit_connections()
    {
        try {
            return current_user_can( $this->get_ptype()->cap->edit_posts );
        } catch ( Exception $e ) {
            trigger_error( $e->getMessage(), E_USER_WARNING );

            return false;
        }
    }

    public function can_create_item()
    {
        if (count( $this->query_vars['post_type'] ) > 1) {
            return false;
        }

        if (count( $this->query_vars ) > 1) {
            return false;
        }

        return true;
    }

    public function translate_qv($qv)
    {
        $map = array(
            'include'  => 'post__in',
            'exclude'  => 'post__not_in',
            'search'   => 's',
            'page'     => 'paged',
            'per_page' => 'posts_per_page'
        );

        foreach ($map as $old => $new) {
            if (isset( $qv["p2p:$old"] )) {
                $qv[$new] = _p2p_pluck( $qv, "p2p:$old" );
            }
        }

        return $qv;
    }

    public function do_query($args)
    {
        return new \WP_Query( $args );
    }

    public function capture_query($args)
    {
        $q = new \WP_Query;
        $q->_p2p_capture = true;

        $q->query( $args );

        return $q->_p2p_sql;
    }

    public function get_list(\WP_Query $wp_query)
    {
        $list = new ItemList( $wp_query->posts, $this->item_type );

        $list->current_page = max( 1, $wp_query->get( 'paged' ) );
        $list->total_pages = $wp_query->max_num_pages;

        return $list;
    }

    public function is_indeterminate($side)
    {
        $common = array_intersect(
            $this->query_vars['post_type'],
            $side->query_vars['post_type']
        );

        return !empty( $common );
    }

    protected function recognize($arg)
    {
        if (is_object( $arg ) && !isset( $arg->post_type )) {
            return false;
        }

        $post = get_post( $arg );

        if (!is_object( $post )) {
            return false;
        }

        if (!$this->recognize_post_type( $post->post_type )) {
            return false;
        }

        return $post;
    }

    public function recognize_post_type($post_type)
    {
        if (!post_type_exists( $post_type )) {
            return false;
        }

        return in_array( $post_type, $this->query_vars['post_type'] );
    }
}
