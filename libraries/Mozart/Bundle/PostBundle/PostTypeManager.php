<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PostBundle;


class PostTypeManager
{
    private $post_types;

    public function __construct()
    {
        $this->post_types = array();
    }

    public function registerPostType( PostTypeInterface $post_type )
    {
        $this->post_types[$post_type->getKey()] = $post_type;
    }

    public function getPostTypes()
    {
        return $this->post_types;
    }

} 