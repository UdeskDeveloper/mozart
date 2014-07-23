<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

$applicationConfig = array(
    /* --------------------------------------------------------------- */
    // Plugin textdomain
    /* --------------------------------------------------------------- */
    'textdomain'    => 'themosis',

    /* --------------------------------------------------------------- */
    // Global Javascript namespace of your application
    /* --------------------------------------------------------------- */
    'namespace'     => 'themosis',

    /* --------------------------------------------------------------- */
    // Set WordPress admin ajax file without the PHP extension
    /* --------------------------------------------------------------- */
    'ajaxurl'	    => 'admin-ajax',

    /* --------------------------------------------------------------- */
    // Encoding
    /* --------------------------------------------------------------- */
    'encoding'	    => 'UTF-8',

    /* --------------------------------------------------------------- */
    // Rewrite - If you want to modify default WordPress paths.
    // If you change this parameter, you need to go to the 'permalinks'
    // tab in the WordPress admin and update the structure by saving
    // the changes.
    /* --------------------------------------------------------------- */
    'rewrite'	    => false,

    /* --------------------------------------------------------------- */
    // Allow to define the path for the login page
    /* --------------------------------------------------------------- */
    'loginurl'	    => 'login',

    /* --------------------------------------------------------------- */
    // Cleanup Header
    /* --------------------------------------------------------------- */
    'cleanup'	    => true,

    /* --------------------------------------------------------------- */
    // Add custom htaccess settings.
    // The settings are a mix of Mozart parameters and HTML5 Boilerplate
    // htaccess settings.
    // Will overwrite your htaccess settings each time
    // you go to the permalinks settings page in the admin.
    // If you edit your main .htaccess file and you want to avoid the
    // framework to overwrite your settings, set this to "false".
    /* --------------------------------------------------------------- */
    'htaccess'	    => true,

    /* --------------------------------------------------------------- */
    // Restrict access to the WordPress Admin for users with a
    // specific role.
    // Once the theme is activated, you can only log in by going
    // to 'wp-login.php' or 'login' (if permalinks changed) urls.
    // By default, allows 'administrator', 'editor', 'author',
    // 'contributor' and 'subscriber' to access the ADMIN area.
    // Edit this configuration in order to limit access.
    /* --------------------------------------------------------------- */
    'access'	    => array(
        'administrator',
        'editor',
        'author',
        'contributor',
        'subscriber'
    )

);

$parameterBag = new ParameterBag();

foreach ($applicationConfig as $root => $values) {
        $value = $parameterBag->escapeValue( $values );
        $container->setParameter( "mozart.$root", $value );
}