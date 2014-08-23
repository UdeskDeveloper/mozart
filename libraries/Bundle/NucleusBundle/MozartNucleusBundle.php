<?php

namespace Mozart\Bundle\NucleusBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mozart\Bundle\NucleusBundle\DependencyInjection\Security\Factory\WordpressFactory;
use Doctrine\DBAL\Types\Type;
use Mozart\Bundle\NucleusBundle\Types\WordpressMetaType;
use Mozart\Bundle\NucleusBundle\Types\WordpressIdType;

/**
 * Class MozartNucleusBundle
 *
 * @package Mozart\Bundle\NucleusBundle
 */
class MozartNucleusBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build( $container );

        // Security
        $container->getExtension( 'security' )->addSecurityListenerFactory( new WordpressFactory() );
    }

    public function boot()
    {
        parent::boot();

        if (!Type::hasType( WordpressMetaType::NAME )) {
            Type::addType( WordpressMetaType::NAME, 'Mozart\Bundle\NucleusBundle\Types\WordpressMetaType' );
        }

        if (!Type::hasType( WordpressIdType::NAME )) {
            Type::addType( WordpressIdType::NAME, 'Mozart\Bundle\NucleusBundle\Types\WordpressIdType' );
        }
    }
}
