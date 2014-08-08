<?php
namespace Mozart\Component\Post\Connection\Side;

class SideUser extends Side
{
    protected $item_type = 'P2P_Item_User';

    public function __construct($query_vars)
    {
        $this->query_vars = $query_vars;
    }

    public function get_object_type()
    {
        return 'user';
    }

    public function get_desc()
    {
        return __( 'Users', P2P_TEXTDOMAIN );
    }

    public function get_title()
    {
        return $this->get_desc();
    }

    public function get_labels()
    {
        return (object) array(
            'singular_name' => __( 'User', P2P_TEXTDOMAIN ),
            'search_items' => __( 'Search Users', P2P_TEXTDOMAIN ),
            'not_found' => __( 'No users found.', P2P_TEXTDOMAIN ),
        );
    }

    public function can_edit_connections()
    {
        return current_user_can( 'list_users' );
    }

    public function can_create_item()
    {
        return false;
    }

    public function translate_qv($qv)
    {
        if ( isset( $qv['p2p:include'] ) )
            $qv['include'] = _p2p_pluck( $qv, 'p2p:include' );

        if ( isset( $qv['p2p:exclude'] ) )
            $qv['exclude'] = _p2p_pluck( $qv, 'p2p:exclude' );

        if ( isset( $qv['p2p:search'] ) && $qv['p2p:search'] )
            $qv['search'] = '*' . _p2p_pluck( $qv, 'p2p:search' ) . '*';

        if ( isset( $qv['p2p:page'] ) && $qv['p2p:page'] > 0 ) {
            if ( isset( $qv['p2p:per_page'] ) && $qv['p2p:per_page'] > 0 ) {
                $qv['number'] = $qv['p2p:per_page'];
                $qv['offset'] = $qv['p2p:per_page'] * ( $qv['p2p:page'] - 1 );
            }
        }

        return $qv;
    }

    public function do_query($args)
    {
        return new WP_User_Query( $args );
    }

    public function capture_query($args)
    {
        $args['count_total'] = false;

        $uq = new WP_User_Query;
        $uq->_p2p_capture = true; // needed by P2P_URL_Query

        // see http://core.trac.wordpress.org/ticket/21119
        $uq->query_vars = wp_parse_args( $args, array(
            'blog_id' => $GLOBALS['blog_id'],
            'role' => '',
            'meta_key' => '',
            'meta_value' => '',
            'meta_compare' => '',
            'include' => array(),
            'exclude' => array(),
            'search' => '',
            'search_columns' => array(),
            'orderby' => 'login',
            'order' => 'ASC',
            'offset' => '',
            'number' => '',
            'count_total' => true,
            'fields' => 'all',
            'who' => ''
        ) );

        $uq->prepare_query();

        return "SELECT $uq->query_fields $uq->query_from $uq->query_where $uq->query_orderby $uq->query_limit";
    }

    public function get_list($query)
    {
        $list = new P2P_List( $query->get_results(), $this->item_type );

        $qv = $query->query_vars;

        if ( isset( $qv['p2p:page'] ) ) {
            $list->current_page = $qv['p2p:page'];
            $list->total_pages = ceil( $query->get_total() / $qv['p2p:per_page'] );
        }

        return $list;
    }

    public function is_indeterminate($side)
    {
        return true;
    }

    public function get_base_qv($q)
    {
        return array_merge( $this->query_vars, $q );
    }

    protected function recognize($arg)
    {
        if ( is_a( $arg, 'WP_User' ) )
            return $arg;

        return get_user_by( 'id', $arg );
    }
}
