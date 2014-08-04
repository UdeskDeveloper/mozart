<?php
namespace Mozart\Component\Post\Connection;

class item-any extends P2P_Item
{
    public function __construct() {}

    public function get_permalink() {}

    public function get_title() {}

    public function get_object()
    {
        return 'any';
    }

    public function get_id()
    {
        return false;
    }
}
