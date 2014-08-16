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
    {}

    public function registerPages(){}

    /**
     * @param $key
     * @return mixed
     */
    public function getPage($key)
    {
        return $this->pages[$key];
    }

    /**
     * @return array
     */
    public function getPages()
    {
        return $this->pages;
    }
}
