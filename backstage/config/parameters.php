<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

// what if we are not in WordPress mode
if (false === defined( 'ABSPATH' )) {

    define( 'WP_USE_THEMES', false );

    // let's find wp-load.php
    $finder = new Symfony\Component\Finder\Finder();

    $finder->files()
        ->name( 'wp-load.php' )
        ->ignoreUnreadableDirs()
        ->depth( '== 0' )
        ->in( __DIR__ . '/../../' )
        ->in( __DIR__ . '/../../../' )
        ->in( __DIR__ . '/../../../../' )
        ->in( __DIR__ . '/../../../../../' )
        ->in( __DIR__ . '/../../../../../../' )
        ->in( __DIR__ . '/../../../../../../../' )
        ->in( __DIR__ . '/../../../../../../../../' );

    foreach ($finder as $file) {
        require_once( $file->getRealpath() );
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
}

$container->setParameter( 'database_driver', 'pdo_mysql' );
$container->setParameter( 'database_host', DB_HOST );
$container->setParameter( 'database_charset', DB_CHARSET );
$container->setParameter( 'database_collate', DB_COLLATE );
$container->setParameter( 'database_port', 3307 );
$container->setParameter( 'database_name', DB_NAME );
$container->setParameter( 'database_user', DB_USER );
$container->setParameter( 'database_password', DB_PASSWORD );

global $wpdb;
$container->setParameter( 'database_table_prefix', $wpdb->prefix );

$container->setParameter( 'mailer_transport', 'smtp' );
$container->setParameter( 'mailer_host', '127.0.0.1' );
$container->setParameter( 'mailer_user', '' );
$container->setParameter( 'mailer_password', '' );

if ('' === WPLANG) {
    $container->setParameter( 'locale', 'en' );
} else {
    // the WPLANG usually looks like this: de_DE, ro_RO
    $locale = explode( '_', WPLANG );
    $container->setParameter( 'locale', $locale[0] );
}

$container->setParameter( 'secret', NONCE_KEY );

$container->setParameter( 'debug_toolbar', true );
$container->setParameter( 'debug_redirects', false );
$container->setParameter( 'use_assetic_controller', false );
