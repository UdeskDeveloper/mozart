<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config\Page;


/**
 * Class ConfigPageManager
 * @package Mozart\Component\Config\Page
 */
abstract class AbstractConfigPageManager
{
    /**
     * @var array
     */
    protected $pages;

    /**
     *
     */
    public function __construct()
    {
        $this->pages = array();
    }

    /**
     * @param ConfigPageInterface $configPage
     */
    public function registerPage(ConfigPageInterface $configPage)
    {
        if ($configPage->getMenuPosition()) {
            $this->pages[$configPage->getMenuPosition()][$configPage->getKey()] = $configPage;
        } else {
            $this->pages[] = array(
                $configPage->getKey() => $configPage
            );
        }
    }

    abstract public function registerPages();

    /**
     * @return array
     */
    public function getPages()
    {
        return $this->pages;
    }
}
