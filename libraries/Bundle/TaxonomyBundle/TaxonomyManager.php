<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\TaxonomyBundle;

/**
 * Class TaxonomyManager
 *
 * @package Mozart\Bundle\TaxonomyBundle
 */
class TaxonomyManager
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
     * @return TaxonomyInterface[]
     */
    public function getTaxonomies()
    {
        return $this->taxonomies;
    }

    /**
     * @param TaxonomyInterface $taxonomy
     */
    public function registerTaxonomy(TaxonomyInterface $taxonomy)
    {
        $this->taxonomies[$taxonomy->getName()] = $taxonomy;
    }

	public function onWordpressInit()
	{
		foreach ($this->getTaxonomies() as $name => $taxonomy) {
			register_taxonomy( $name, $taxonomy->getObjectTypes(), $taxonomy->getArguments() );
		}
	}
}
