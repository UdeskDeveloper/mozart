<?php

namespace Mozart\Bundle\BlogBundle\Event;

use  Mozart\Bundle\BlogBundle\Model\Blog;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class SwitchBlogEvent
 *
 * @package Mozart\Bundle\BlogBundle\Event
 */
class BlogEvent extends Event
{
    const TYPE_SWITCH_BLOG = 'wordpress.blog.switch_blog';
    /**
     * @var \Mozart\Bundle\BlogBundle\Model\Blog
     */
    protected $blog;

    /**
     * @param Blog $blog
     */
    public function __construct(Blog $blog)
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
