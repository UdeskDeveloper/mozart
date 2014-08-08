<?php
namespace Mozart\Bundle\PostBundle\Admin\Connection\Field;

/**
 * A P2P admin metabox is composed of several "fields".
 */
interface FieldInterface
{
    public function get_title();
    public function render( $p2p_id, $item );
}
