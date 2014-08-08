<?php

namespace  Mozart\Bundle\PostBundle\Model;

interface PostManagerInterface
{
    public function findOnePostById($id);

    public function findOnePostBySlug($slug);
}
