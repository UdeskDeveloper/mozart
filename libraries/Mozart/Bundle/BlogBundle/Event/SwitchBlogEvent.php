<?php

namespace Mozart\Bundle\BlogBundle\Event;

use  Mozart\Bundle\BlogBundle\Model\Blog;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class SwitchBlogEvent
 *
 * @package Mozart\Bundle\BlogBundle\Event
 */
class SwitchBlogEvent extends Event
{
    /**
     * @var \Mozart\Bundle\BlogBundle\Model\Blog
     */
    protected $blog;

    /**
     * @param Blog $blog
     */
    public function __construct( Blog $blog )
    {
        $this->blog = $blog;
    }

    /**
     * @return Blog
     */
    public function getBlog()
    {
        return $this->blog;
    }
}