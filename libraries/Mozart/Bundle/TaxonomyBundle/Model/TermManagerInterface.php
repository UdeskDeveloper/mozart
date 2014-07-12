<?php

namespace Mozart\Bundle\TaxonomyBundle\Model;

use Mozart\Bundle\PostBundle\Model\Post;

interface TermManagerInterface
{
    public function findTermsByPost(Post $post, Taxonomy $taxonomy = null);
}
