<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Option\Extension;

/**
 * Class ExtensionManager
 * @package Mozart\Component\Option\Extension
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
    public function addExtension(ExtensionInterface $extension)
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

    public function loadExtensions() {

        foreach ($this->getExtensions() as $extension) {
//            $extension->extend( $this );
        }
    }
}
