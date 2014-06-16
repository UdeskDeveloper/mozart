<?php

namespace Mozart\Bundle\BackofficeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mozart\Bundle\BackofficeBundle\DependencyInjection\Compiler\ReduxSectionsCompilerPass;

/**
 * Class MozartBackofficeBundle
 *
 * @package Mozart\Bundle\BackofficeBundle
 */
class MozartBackofficeBundle extends Bundle
{
    protected $optionsManager;
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ReduxSectionsCompilerPass);
    }

    /**
     *
     */
    public function boot()
    {
        if (\Mozart::isWpRunning() === false) {
            return;
        }
        add_image_size('admin-thumb', 80, 9999);
        add_action('login_head', array(&$this, 'login_head'));
        add_action('admin_head', array(&$this, 'load_styles'));
        add_action('admin_menu', array(&$this, 'admin_menu_separator'));

        $this->optionsManager = new Redux\Configuration();
        $this->optionsManager->init(array(), $this->container);

        $this->optionsExtensionManager = new Redux\Extensions\Configuration();
        $this->optionsExtensionManager->init();

        $this->redux = new Redux\Redux();
    }


    /**
     *
     */
    public function login_head()
    {
        if (parameter('admin', 'login_screen', 'default') != 'on') {
            echo "
        <style>
        body.login #login h1 a {
            background: url('/wp-content/mozart/public/bundles/mozartbackoffice/img/admin.png') no-repeat scroll center top transparent;
            height: 241px;
        width: auto;
        }
        </style>";
        }
        if (parameter('admin', 'login_screen', 'image')) {
            echo "
        <style>
        body.login #login h1 a {
            background: url('" . parameter('admin', 'login_screen', 'image') . "') no-repeat scroll center top transparent;
            height: 241px;
        }
        </style>";
        }
    }

    /**
     *
     */
    public function load_styles()
    {
        if (is_admin()) {
            wp_register_style('admin-css', get_stylesheet_directory_uri()
                    . '/wp-content/mozart/public/bundles/mozartbackoffice/css/admin.css');
            wp_enqueue_style('admin-css');
        }
    }

    /**
     * @param $position
     */
    public function add_admin_menu_separator($position)
    {
        global $menu;
        $index = 0;

        foreach ($menu as $offset => $section) {
            if (substr($section[2], 0, 9) == 'separator')
                $index ++;
            if ($offset >= $position) {
                $menu[$position] = array('', 'read', "separator{$index}", '', 'wp-menu-separator');
                break;
            }
        }

        ksort($menu);
    }

    /**
     *
     */
    public function admin_menu_separator()
    {
        $this->add_admin_menu_separator(30);
        $this->add_admin_menu_separator(50);
    }

}
