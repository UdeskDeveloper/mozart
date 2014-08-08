<?php
namespace Mozart\Component\Post\Connection\Item;

/**
 * A uniform wrapper for various types of WP objects, i.e. posts or users.
 */
abstract class Item implements ItemInterface
{
    protected $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function __isset($key)
    {
        return isset( $this->item->$key );
    }

    public function __get($key)
    {
        return $this->item->$key;
    }

    public function __set($key, $value)
    {
        $this->item->$key = $value;
    }

    public function get_object()
    {
        return $this->item;
    }

    public function get_id()
    {
        return $this->item->ID;
    }

    abstract public function get_permalink();

    abstract public function get_title();
}
