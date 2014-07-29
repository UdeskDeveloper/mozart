<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Cache\Transient;

interface TransientInterface
{
    public function get($transient);

    public function delete($transient);

    public function set($transient, $value, $expiration = 0);
}
