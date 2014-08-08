<?php

$environment = 'prod';
$debug = false;

if (defined( 'WP_DEBUG' ) && WP_DEBUG) {
    $environment = 'dev';
    $debug = true;
}

if (false === $debug) {
    $loader = require_once __DIR__ . '/bootstrap.php.cache';
} else {
    $loader = require_once __DIR__.'/autoload.php';
}

// Use APC for autoloading to improve performance.
if (defined( 'WP_DEBUG' ) && false === WP_DEBUG && extension_loaded( 'apc' )) {
    $apcLoader = new Symfony\Component\ClassLoader\ApcClassLoader( 'mozart', $loader );
    $loader->unregister();
    $apcLoader->register( true );
}

//if (true === $debug) {
//    Symfony\Component\Debug\Debug::enable(  );
//}

require_once __DIR__ . '/MozartKernel.php';
//require_once __DIR__.'/MozartCache.php';

$kernel = new MozartKernel( $environment, $debug );
if (false === $debug) {
    $kernel->loadClassCache();
}

// $kernel = new MozartCache($kernel);
// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
// Request::enableHttpMethodParameterOverride();

$kernel->boot();

Mozart::setContainer( $kernel->getContainer() );

do_action('mozart.init');

add_action(
    'wp_loader',
    function () use ( $kernel ) {
        $kernel->shutdown();
    },
    999
);
