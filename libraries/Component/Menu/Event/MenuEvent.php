<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Menu\Event;

use Symfony\Component\EventDispatcher\Event;

class MenuEvent extends Event
{
    private $adminMenuOrder = array();
    private $menu;

    /**
     * @return array
     */
    public function getAdminMenuOrder()
    {
        return $this->adminMenuOrder;
    }

    /**
     * @param array $adminMenuOrder
     */
    public function setAdminMenuOrder($adminMenuOrder)
    {
        $this->adminMenuOrder = $adminMenuOrder;
    }

     /**
     * @return mixed
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param mixed $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }
}
