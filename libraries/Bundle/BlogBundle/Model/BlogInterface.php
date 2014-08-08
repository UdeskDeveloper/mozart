<?php

namespace  Mozart\Bundle\BlogBundle\Model;

use  Mozart\Bundle\NucleusBundle\Doctrine\WordpressEntityManager;

interface BlogInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @return WordpressEntityManager
     */
    public function getEntityManager();
}
