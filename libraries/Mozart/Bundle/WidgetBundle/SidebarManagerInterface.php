<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle;

interface SidebarManagerInterface
{
    public function getSidebars();

    public function getSidebar( $key );

    public function registerSidebar( SidebarInterface $sidebar );
}
