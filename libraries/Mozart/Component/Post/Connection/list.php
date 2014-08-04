<?php
namespace Mozart\Component\Post\Connection;

class list
{
    public $items;
    public $current_page = 1;
    public $total_pages = 0;

    public function __construct($items, $item_type)
    {
        if ( is_numeric( reset( $items ) ) ) {
            // Don't wrap when we just have a list of ids
            $this->items = $items;
        } else {
            $this->items = _p2p_wrap( $items, $item_type );
        }
    }
}
