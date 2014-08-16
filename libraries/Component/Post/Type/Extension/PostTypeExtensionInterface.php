<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Post\Type\Extension;

use Mozart\Component\Post\Type\PostTypeInterface;

interface PostTypeExtensionInterface
{
    public function getKey();

    /**
     * @param PostTypeInterface $postType
     * @return mixed
     */
    public function load( PostTypeInterface $postType );
}
