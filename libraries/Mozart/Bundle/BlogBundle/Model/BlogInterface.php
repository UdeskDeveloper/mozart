<?php

namespace  Mozart\Bundle\BlogBundle\Model;

use  Mozart\Bundle\BlogBundle\Doctrine\WordpressEntityManager;

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
