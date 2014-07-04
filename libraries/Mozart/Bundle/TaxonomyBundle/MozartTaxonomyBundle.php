<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\TaxonomyBundle;

use Mozart\Bundle\TaxonomyBundle\DependencyInjection\Compiler\TaxonomiesCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class MozartTaxonomyBundle
 *
 * @package Mozart\Bundle\TaxonomyBundle
 */
class MozartTaxonomyBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build( $container );
        $container->addCompilerPass( new TaxonomiesCompilerPass );
    }

    /**
     *
     */
    public function boot()
    {
        add_action( 'init', array($this, 'registerTaxonomies'), 0 );
    }

    /**
     *
     */
    public function registerTaxonomies()
    {
        if (!$this->container->has( 'mozart_taxonomy.taxonomy_manager' )) {
            return;
        }

        $taxonomies = $this->container->get( 'mozart_taxonomy.taxonomy_manager' )
            ->getTaxonomies();

        foreach ($taxonomies as $name => $taxonomy) {
            register_taxonomy( $name, $taxonomy->getObjectTypes(), $taxonomy->getArguments() );
        }
    }

}
