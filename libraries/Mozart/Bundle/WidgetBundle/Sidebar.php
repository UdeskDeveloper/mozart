<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle;

use Symfony\Component\DependencyInjection\Container;

class Sidebar implements SidebarInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        $className     = get_class( $this );
        $classBaseName = substr( strrchr( $className, '\\' ), 1 );

        $name = Container::underscore( $classBaseName );

        return __( ucwords( str_replace( '_', ' ', $name ) ), 'mozart' );
    }

    public function getKey()
    {
        $className     = get_class( $this );
        $classBaseName = substr( strrchr( $className, '\\' ), 1 );
        $classBaseName = str_replace( 'Sidebar', '', $classBaseName );

        return 'sidebar-' . str_replace( '_', '-', Container::underscore( $classBaseName ) );
    }

    public function getConfiguration()
    {
        return array(
            'name'          => $this->getName(),
            'id'            => $this->getKey(),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>'
        );
    }
}
