<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\MenuBundle\Admin;

/**
 * Class AdminMenuManager
 *
 * @package Mozart\Bundle\MenuBundle\Admin
 */
class AdminMenuManager
{
    /**
     * @var AdminMenuInterface[]
     */
    protected $menus;

    /**
     *
     */
    public function __construct()
    {
        $this->menus = array();

    }

    /**
     * @param AdminMenuInterface $menu
     */
    public function addMenu(AdminMenuInterface $menu)
    {
        $this->menus[$menu->getAlias()] = $menu;
    }

    /**
     * @return AdminMenuInterface[]
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * @param $alias
     *
     * @return AdminMenuInterface
     */
    public function getMenu($alias)
    {
        return $this->menus[$alias];
    }

}
