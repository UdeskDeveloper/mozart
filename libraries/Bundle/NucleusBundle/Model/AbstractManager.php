<?php

namespace Mozart\Bundle\NucleusBundle\Model;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class AbstractManager
 *
 * @package Mozart\Bundle\NucleusBundle\Model
 */
abstract class AbstractManager
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return object
     */
    protected function getEntityManager()
    {
        $entityManagerName = $this->container->getParameter( 'wordpress.entity_manager' );

        return $this->container->get( 'doctrine.orm.' . $entityManagerName . '_entity_manager' );
    }
}
