<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\AjaxBundle;

use Mozart\Component\Ajax\Ajax;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MozartAjaxBundle extends Bundle
{
    public function boot()
    {
        add_action( 'wp_head', array( $this, 'addAjaxScript' ) );
    }

    public function addAjaxScript()
    {
        Ajax::installScript( 'Mozart' );
    }

}
