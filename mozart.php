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

add_action( 'plugins_loaded', 'mozart_start_concert' );

if (is_admin()) {
	function mozart_wordpress_activation_hook()
	{
		/*
		 * TODO: add activation hook
		 *
		 * check for Symfony 2 requirements, use /backstage/check.php
		 * check if the current theme has Mozart support.
		 * For a theme to have Mozart support, it needs to have a composer.json
		 * in its directory root, in which specifies a file to be autoloaded,
		 * file that contains stuff to be executed when the bundles are initialized,
		 * like adding a filter for "register_mozart_bundle" to register the theme bundles
		 *
		 */

	}

	register_activation_hook( __FILE__, 'mozart_wordpress_activation_hook' );

	function mozart_wordpress_uninstall_hook()
	{
		// TODO: add unninstall hook
	}

	register_uninstall_hook( __FILE__, 'mozart_wordpress_uninstall_hook' );
}