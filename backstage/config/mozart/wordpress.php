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
    )
);
/*
 * Sometimes the keys and salts contains more than one "%"
 * Symfony will interpret this as being a reference to another parameter
 * To avoid that, we need to escape those values
 * We use the escapeValue() method offered by Symfony\Component\DependencyInjection\ParameterBag\ParameterBag
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