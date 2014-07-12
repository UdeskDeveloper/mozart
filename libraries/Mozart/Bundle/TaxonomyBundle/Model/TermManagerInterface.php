<?php

namespace Mozart\Bundle\TaxonomyBundle\Model;

interface TermManagerInterface
{
    public function findTermsByPost(Post $post, Taxonomy $taxonomy = null);
}
