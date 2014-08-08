<?php
namespace Mozart\Component\Post\Connection\Item;

class ItemUser extends Item
{
    public function get_title()
    {
        return $this->item->display_name;
    }

    public function get_permalink()
    {
        return get_author_posts_url( $this->item->ID );
    }

    public function get_editlink()
    {
        return get_edit_user_link( $this->item->ID );
    }
}
