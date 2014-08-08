<?php
namespace Mozart\Component\Post\Connection\Side;

use Mozart\Component\Post\Connection\Item\Item;

abstract class Side
{
    protected $item_type;

    abstract public function get_object_type();

    abstract public function get_title();

    abstract public function get_desc();

    abstract public function get_labels();

    abstract public function can_edit_connections();

    abstract public function can_create_item();

    abstract public function get_base_qv($q);

    abstract public function translate_qv($qv);

    abstract public function do_query($args);

    abstract public function capture_query($args);

    abstract public function get_list(\WP_Query $query);

    abstract public function is_indeterminate($side);

    final public function is_same_type(Side $side)
    {
        return $this->get_object_type() == $side->get_object_type();
    }

    /**
     * @return bool|Item
     */
    public function item_recognize($arg)
    {
        $class = $this->item_type;

        if (is_a( $arg, 'Item' )) {
            if (!is_a( $arg, $class )) {
                return false;
            }

            $arg = $arg->get_object();
        }

        $raw_item = $this->recognize( $arg );
        if (!$raw_item) {
            return false;
        }

        return new $class( $raw_item );
    }

    abstract protected function recognize($arg);
}
