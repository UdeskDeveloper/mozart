<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config\Storage;

use Mozart\Component\Config\StorageInterface;

class DatabaseStorage implements StorageInterface
{
    public function get($name)
    {
        return get_option($name);
    }

    public function getAll()
    {
        return false;
    }
}
