<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PostBundle;

interface PostTypeExtensionInterface
{
    public function getKey();

    public function load( PostType $postType );
}
