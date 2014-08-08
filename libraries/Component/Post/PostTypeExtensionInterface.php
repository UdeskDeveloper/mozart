<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Post;

interface PostTypeExtensionInterface
{
    public function getKey();

    public function load( PostType $postType );
}
