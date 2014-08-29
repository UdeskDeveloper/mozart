<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\MenuBundle\EventListener;

use Mozart\Component\Menu\Event\MenuEvent;
use Mozart\Component\Menu\MenuEvents;

class MenuEventListener
{
    public function onKernelBoot()
    {
        add_filter( 'custom_menu_order', '__return_true' );
        add_filter(
            MenuEvents::ORDER,
            function ($menuOrder) {
                $event = new MenuEvent();
                $event->setAdminMenuOrder( $menuOrder );
                /** @var MenuEvent $event */
                $event = \Mozart::dispatch( MenuEvents::ORDER, $event );

                return $event->getAdminMenuOrder();
            },
            0
        );

        /**
         * @see add_menu_classes
         */
        add_filter(
            MenuEvents::FILTER,
            function ($menu) {
                $event = new MenuEvent();
                $event->setMenu( $menu );
                /** @var MenuEvent $event */
                $event = \Mozart::dispatch( MenuEvents::FILTER, $event );

                return $event->getMenu();
            },
            10,
            1
        );

        add_filter(
            'admin_print_scripts',
            function () {
                wp_enqueue_script(
                    'mozart-menu-delete',
                    plugins_url( '/mozart/public/bundles/mozart/menu/js/delete.js' )
                );
            }
        );
    }
}
