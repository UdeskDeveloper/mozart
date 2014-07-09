<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\CacheBundle\Transient;

interface TransientInterface
{
    public function get( $transient );

    public function delete( $transient );

    public function set( $transient, $value, $expiration = 0 );
}
