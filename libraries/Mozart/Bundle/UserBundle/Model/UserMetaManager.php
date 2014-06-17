<?php

namespace Mozart\Bundle\UserBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Container;
use Mozart\Bundle\NucleusBundle\Model\AbstractManager;

/**
 * Class UserMetaManager
 *
 * @package Mozart\Bundle\UserBundle\Model
 */
class UserMetaManager extends AbstractManager implements UserMetaManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     */
    public function __construct( Container $container )
    {
        parent::__construct( $container );

        $this->em         = $this->getEntityManager();
        $this->repository = $this->em->getRepository( 'MozartUserBundle:UserMeta' );
    }

    /**
     * @param User     $user
     * @param UserMeta $meta
     */
    public function addMeta( User $user, UserMeta $meta )
    {
        $user->addMeta( $meta );
    }

    /**
     * @param User $user
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findAllMetasByUser( User $user )
    {
        return $user->getMetas();
    }

    /**
     * @param array $criteria
     *
     * @return array
     */
    public function findMetasBy( array $criteria )
    {
        return $this->repository->findBy( $criteria );
    }

    /**
     * @param array $criteria
     *
     * @return null|object
     */
    public function findOneMetaBy( array $criteria )
    {
        return $this->repository->findOneBy( $criteria );
    }
}
