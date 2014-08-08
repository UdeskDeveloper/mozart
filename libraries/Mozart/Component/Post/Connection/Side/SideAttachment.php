<?php
namespace Mozart\Component\Post\Connection\Side;

class SideAttachment extends SidePost
{
    protected $item_type = 'P2P_Item_Attachment';

    public function __construct($query_vars)
    {
        $this->query_vars = $query_vars;

        $this->query_vars['post_type'] = array( 'attachment' );
    }

    public function can_create_item()
    {
        return false;
    }

    public function get_base_qv($q)
    {
        return array_merge( parent::get_base_qv( $q ), array(
            'post_status' => 'inherit'
        ) );
    }
}
