<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Widget\Sidebar\Customizer;


use Mozart\Component\Widget\Sidebar\SidebarCustomizer;

/**
 * Extends the widgets section to add the custom sidebars UI elements.
 *
 * Class WidgetManager
 * @package Mozart\Component\Widget\Sidebar\Customizer
 */
class WidgetManager
{
    public function __construct()
    {
        if (is_admin()) {
            add_action(
                'widgets_admin_page',
                array( $this, 'widget_sidebar_content' )
            );
        }
    }

    /**
     * Adds the additional HTML code to the widgets section.
     */
    public function widget_sidebar_content()
    {
        include CSB_VIEWS_DIR . 'widgets.php';
    }
} 