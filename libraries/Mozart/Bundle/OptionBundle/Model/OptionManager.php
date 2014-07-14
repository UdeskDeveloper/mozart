<?php

namespace  Mozart\Bundle\OptionBundle\Model;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Mozart\Bundle\NucleusBundle\Model\AbstractManager;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class OptionManager
 * @package Mozart\Bundle\OptionBundle\Model
 */
class OptionManager extends AbstractManager implements OptionManagerInterface
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
     * @var ArrayCache
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->em         = $this->getEntityManager();
        $this->repository = $this->em->getRepository('MozartOptionBundle:Option');
        $this->cache      = new ArrayCache();
    }

    /**
     * @param $name
     * @return bool|mixed|Option|string
     */
    public function findOneOptionByName($name)
    {
        if (false === $option = $this->cache->fetch($name)) {
            /** @var $option Option */
            $option = $this->repository->findOneBy(array(
                'name' => $name
            ));

            if ($option !== null) {
                $this->cacheOption($option);
            }
        }

        return $option;
    }

    /**
     * @param Option $option
     */
    private function cacheOption(Option $option)
    {
        $this->cache->save($option->getName(), clone $option);
    }
}
