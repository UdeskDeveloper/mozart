<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\AdminBundle;


use Mozart\Component\Admin\AdminEvents;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MozartAdminBundle extends Bundle
{
    public function boot()
    {
        add_filter(
            AdminEvents::HEAD,
            function () {
                \Mozart::dispatch( AdminEvents::HEAD );
            },
            0
        );
        add_filter(
            AdminEvents::MENU,
            function () {
                \Mozart::dispatch( AdminEvents::MENU );
            },
            0
        );
    }

} 