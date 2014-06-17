<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\TaxonomyBundle;


/**
 * Class TaxonomyChain
 *
 * @package Mozart\Bundle\TaxonomyBundle
 */
class TaxonomyChain
{
    /**
     * @var TaxonomyInterface[]
     */
    protected $taxonomies = array();

    /**
     *
     */
    public function __construct()
    {
        $this->taxonomies = array();
    }

    /**
     * @return array
     */
    public function getTaxonomies()
    {
        return $this->taxonomies;
    }

    /**
     * @param TaxonomyInterface $taxonomy
     */
    public function registerTaxonomy( TaxonomyInterface $taxonomy )
    {
        $this->taxonomies[$taxonomy->getName()] = $taxonomy;
    }
} 