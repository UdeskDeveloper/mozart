<?php

/*
Plugin Name: Mozart
Description: Mozart is a web application framework for WordPress.
Author: Alexandru Furculita (rhetina)
Author URI: http://www.rhetina.com/
Text Domain: mozart
Domain Path: /backstage/translations
Version: 1.0.0
*/

/*
 * let's prepare our headphones and start listening to Mozart
 */
function mozart_start_concert()
{
    require_once __DIR__ . '/backstage/bootstrap.php';
}

add_action('plugins_loaded', 'mozart_start_concert');

if (is_admin()) {
    function mozart_wordpress_activation_hook()
    {
        require_once __DIR__ . '/backstage/autoload.php';
        require_once __DIR__ . '/backstage/AppKernel.php';
        AppKernel::onActivation();
    }
    register_activation_hook(__FILE__, 'mozart_wordpress_activation_hook');

    function mozart_wordpress_uninstall_hook()
    {
        require_once __DIR__ . '/backstage/bootstrap.php';
        try {
            \Rhetina::service('kernel')->onUninstall();
        } catch (\NotInstalledException $e) {
            return;
        }
    }
    register_uninstall_hook(__FILE__, 'mozart_wordpress_uninstall_hook');
}
