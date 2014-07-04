<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\TaxonomyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TaxonomiesCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition( 'mozart_taxonomy.taxonomy_manager' )) {
            return;
        }

        $definition = $container->getDefinition(
            'mozart_taxonomy.taxonomy_manager'
        );

        foreach ($container->findTaggedServiceIds( 'wordpress.taxonomy' ) as $id => $attributes) {
            $definition->addMethodCall(
                'registerTaxonomy',
                array( new Reference( $id ) )
            );
        }
    }

}
