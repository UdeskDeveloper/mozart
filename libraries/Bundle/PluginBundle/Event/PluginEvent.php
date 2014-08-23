<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PluginBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class PluginEvent extends Event
{
    private $baseName = '';
    private $networkDeactivating = false;

    /**
     * @return string
     */
    public function getBaseName()
    {
        return $this->baseName;
    }

    /**
     * @param string $baseName
     */
    public function setBaseName($baseName)
    {
        $this->baseName = $baseName;
    }

    /**
     * @return boolean
     */
    public function isNetworkDeactivating()
    {
        return $this->networkDeactivating;
    }

    /**
     * @param boolean $networkDeactivating
     */
    public function setNetworkDeactivating($networkDeactivating)
    {
        $this->networkDeactivating = $networkDeactivating;
    }

} 