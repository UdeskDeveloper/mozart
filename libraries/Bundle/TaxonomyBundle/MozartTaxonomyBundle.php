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
    }
}
