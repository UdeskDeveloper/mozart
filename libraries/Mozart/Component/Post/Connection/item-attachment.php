<?php
namespace Mozart\Component\Post\Connection;

class item-attachment extends P2P_Item_Post
{
    public function get_title()
    {
        if( wp_attachment_is_image( $this->item->ID ) )

            return wp_get_attachment_image( $this->item->ID, 'thumbnail', false );

        return get_the_title( $this->item );
    }
}
