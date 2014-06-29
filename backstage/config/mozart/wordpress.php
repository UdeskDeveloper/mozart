<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

require_once ABSPATH . 'wp-admin/includes/file.php';

$wordressInfos = array(
    'home'       => array(
        'dir' => get_home_path(),
        'uri' => get_home_url()
    ),
    'site'       => array(
        'uri' => get_site_url()
    ),
    'plugin'     => array(
        'dir' => WP_PLUGIN_DIR,
        'uri' => plugins_url()
    ),
    'theme'      => array(
        'name' => (string)wp_get_theme(),
        'dir'  => get_template_directory(),
        'uri'  => get_template_directory_uri()
    ),
    'stylesheet' => array(
        'dir' => get_stylesheet_directory(),
        'uri' => get_stylesheet_directory_uri()
    ),
    'content'    => array(
        'dir' => WP_CONTENT_DIR,
        'uri' => content_url()
    ),
    'includes'   => array(
        'dir' => WPINC,
        'uri' => includes_url()
    ),
    'key'        => array(
        'auth'        => AUTH_KEY,
        'secure_auth' => SECURE_AUTH_KEY,
        'logged_in'   => LOGGED_IN_KEY,
        'nonce'       => NONCE_KEY
    ),
    'salt'       => array(
        'auth'        => AUTH_SALT,
        'secure_auth' => SECURE_AUTH_SALT,
        'logged_in'   => LOGGED_IN_SALT,
        'nonce'       => NONCE_SALT
    ),
    'cookie'     => array(
        'path'         => COOKIEPATH,
        'domain'       => COOKIE_DOMAIN,
        'site_path'    => SITECOOKIEPATH,
        'admin_path'   => ADMIN_COOKIE_PATH,
        'plugins_path' => PLUGINS_COOKIE_PATH,
        'hash'         => COOKIEHASH,
        'user'         => USER_COOKIE,
        'pass'         => PASS_COOKIE,
        'auth'         => AUTH_COOKIE,
        'secure_auth'  => SECURE_AUTH_COOKIE,
        'logged_in'    => LOGGED_IN_COOKIE,
        'test'         => TEST_COOKIE
    ),
    'env'        => array(
        'memory_limit'     => WP_MEMORY_LIMIT,
        'max_memory_limit' => WP_MAX_MEMORY_LIMIT,
        'debug'            => WP_DEBUG,
        'debug_display'    => WP_DEBUG_DISPLAY,
        'debug_log'        => WP_DEBUG_LOG,
        'debug_queries'    => SAVEQUERIES,
        'cache'            => WP_CACHE,
        'media_trash'      => MEDIA_TRASH,
        'shortinit'        => SHORTINIT
    ),
    'ssl'        => array(
        'force_admin' => FORCE_SSL_ADMIN,
        'force_login' => FORCE_SSL_LOGIN
    )
);
/*
 * Sometimes the keys and salts contains more than one "%"
 * Symfony will interpret this as being a reference to another parameter
 * To avoid that, we need to escape those values
 * We use the escapeValue() method offered by Symfony\Component\DependencyInjection\ParameterBag\ParameterBag
 * It is easy to escape such strings, but let's use something that is there already
 */
$parameterBag = new ParameterBag();

foreach ($wordressInfos as $root => $values) {
    if (is_array( $values )) {
        foreach ($values as $name => $value) {
            $value = $parameterBag->escapeValue( $value );
            $container->setParameter( "wp.$root.$name", $value );
            $container->setParameter( "wordpress.$root.$name", $value );
            unset( $wordressInfos[$root][$name] );
        }
    } else {
        $value = $parameterBag->escapeValue( $values );
        $container->setParameter( "wp.$root", $value );
        $container->setParameter( "wordpress.$root", $value );
        unset( $wordressInfos[$root] );
    }
}
unset( $wordressInfos );
unset( $parameterBag );