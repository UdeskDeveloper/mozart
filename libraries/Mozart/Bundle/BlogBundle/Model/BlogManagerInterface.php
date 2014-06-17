<?php

namespace  Mozart\Bundle\BlogBundle\Model;

interface BlogManagerInterface
{
    /**
     * @param  integer $name
     * @return Blog
     */
    public function findBlogById($id);
}
