<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config;


interface StorageInterface {

    public function get($name);

    public function getAll();
}