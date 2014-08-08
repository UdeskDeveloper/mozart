<?php
namespace Mozart\Component\Post\Connection\Item;

class ItemPost extends Item
{
    public function get_title()
    {
        return get_the_title( $this->item );
    }

    public function get_permalink()
    {
        return get_permalink( $this->item );
    }

    public function get_editlink()
    {
        return get_edit_post_link( $this->item );
    }
}
