<?php

/**
 * A P2P admin metabox is composed of several "fields".
 */
interface P2P_Field
{
    public function get_title();
    public function render( $p2p_id, $item );
}
