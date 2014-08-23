<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config\Page;

class ChildConfigPage extends ConfigPage
{
    /**
     * @var ConfigPageInterface
     */
    protected $parentConfigPage;

    public function __construct(ConfigPageInterface $parentConfigPage)
    {
        $this->parentConfigPage = $parentConfigPage;
    }

    public function getParent()
    {
        return $this->parentConfigPage->getKey();
    }

    public function setParent(ConfigPageInterface $parentConfigPage)
    {
        $this->parentConfigPage = $parentConfigPage;
    }
} 