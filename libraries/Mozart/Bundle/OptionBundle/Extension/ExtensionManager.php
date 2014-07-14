<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\OptionBundle\Extension;


/**
 * Class ExtensionManager
 * @package Mozart\Bundle\OptionBundle\Extension
 */
class ExtensionManager
{

    /**
     * @var ExtensionInterface[]
     */
    private $extensions;

    public function __construct()
    {
        $this->extensions = array();
    }

    /**
     * @param ExtensionInterface $extension
     */
    public function addExtension( ExtensionInterface $extension )
    {
        $this->extensions[] = $extension;
    }

    /**
     * @return ExtensionInterface[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }
} 