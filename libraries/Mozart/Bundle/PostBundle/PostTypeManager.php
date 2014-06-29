<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PostBundle;


/**
 * Class PostTypeManager
 *
 * @package Mozart\Bundle\PostBundle
 */
class PostTypeManager
{
    /**
     * @var PostTypeInterface[]
     */
    private $post_types;

    /**
     *
     */
    public function __construct()
    {
        $this->post_types = array();
    }

    /**
     * @param PostTypeInterface $post_type
     */
    public function registerPostType( PostTypeInterface $post_type )
    {
        $this->post_types[$post_type->getKey()] = $post_type;
    }

    /**
     * @return PostTypeInterface[]
     */
    public function getPostTypes()
    {
        return $this->post_types;
    }

} 