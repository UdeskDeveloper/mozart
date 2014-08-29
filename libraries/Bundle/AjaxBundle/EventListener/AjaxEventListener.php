<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\AjaxBundle\EventListener;

use Mozart\Component\Ajax\Ajax;

class AjaxEventListener
{
    public function onApplicationInit()
    {
        add_action(
            'wp_head',
            function () {
                Ajax::installScript( 'Mozart' );
            }
        );
    }
}
