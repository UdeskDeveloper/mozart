<?php
namespace Mozart\Component\Post\Connection\ItemList;

class ItemList
{
    /**
     * @var
     */
    public $items;
    /**
     * @var int
     */
    public $current_page = 1;
    /**
     * @var int
     */
    public $total_pages = 0;

    /**
     * @param $items
     * @param $item_type
     */
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
