<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\OptionBundle\Redux\Extensions;

/**
 * Class Configuration
 *
 * @package Mozart\Bundle\OptionBundle\Redux\Extensions
 */
class Configuration
{
    /**
     * @param \ReduxFramework $builder
     */
    public function init()
    {
        add_action( "redux/extensions/mozart-options/before", array( $this, 'loadExtensions' ) );
    }

    public function loadExtensions( \ReduxFramework $builder )
    {
        if ($GLOBALS['pagenow'] === "customize.php"
            || $GLOBALS['pagenow'] === "admin-ajax.php"
            || isset( $GLOBALS['wp_customize'] )
        ) {
            new Customizer( $builder, $_REQUEST, $GLOBALS['pagenow'] );
        }
    }
}
