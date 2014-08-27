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
add_action(
    'plugins_loaded',
    function () {
        require __DIR__ . '/backstage/bootstrap.php';
    },
    11
);