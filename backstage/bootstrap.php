<?php
$environment = 'prod';
$debug = false;

if (defined( 'WP_DEBUG' ) && WP_DEBUG) {
    $environment = 'dev';
    $debug = true;
}

//if (false === $debug) {
//    $loader = require_once __DIR__ . '/bootstrap.php.cache';
//} else {
    $loader = require_once __DIR__ . '/autoload.php';
//}

// Use APC for autoloading to improve performance.
if (defined( 'WP_DEBUG' ) && false === WP_DEBUG && extension_loaded( 'apc' )) {
    $apcLoader = new Symfony\Component\ClassLoader\ApcClassLoader( 'mozart', $loader );
    $loader->unregister();
    $apcLoader->register( true );
}

if (true === $debug) {
//    Symfony\Component\Debug\Debug::enable(0);
}

require_once __DIR__ . '/MozartKernel.php';
//require_once __DIR__.'/MozartCache.php';

$kernel = new MozartKernel( $environment, $debug );
$kernel->loadClassCache();

// $kernel = new MozartCache($kernel);
// When using the HttpCache, you need to call the method in
// your front controller instead of relying on the configuration parameter
Symfony\Component\HttpFoundation\Request::enableHttpMethodParameterOverride();

$kernel->boot();

Mozart::setContainer( $kernel->getContainer() );

Mozart::dispatch( Mozart\Bundle\NucleusBundle\MozartEvents::BOOT );

add_action(
	'plugins_loaded',
	function () {
		Mozart::dispatch( Mozart\Bundle\NucleusBundle\MozartEvents::INIT );
	},
	9
);
add_action(
	'init',
	function () {
		Mozart::dispatch( 'init' );
	},
	0
);

add_action(
    'wp_loader',
    function () use (&$kernel) {
        $kernel->shutdown();
    },
    999
);
