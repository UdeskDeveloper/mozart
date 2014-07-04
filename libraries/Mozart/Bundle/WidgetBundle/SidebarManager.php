<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle;

class SidebarManager implements SidebarManagerInterface
{

    protected $sidebars;

    public function __construct()
    {
        $this->sidebars = array();
    }

    public function getSidebars()
    {
        return $this->sidebars;
    }

    public function getSidebar($key)
    {
        return $this->sidebars[$key];
    }

    public function registerSidebar(SidebarInterface $sidebar)
    {
        $this->sidebars[$sidebar->getKey()] = $sidebar;
    }
}
