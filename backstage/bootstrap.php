<?php

// what if we are not in WordPress mode
// TODO: rethink these as it does not work well in console
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

$loader = require_once __DIR__ . '/bootstrap.php.cache';

// Use APC for autoloading to improve performance.
/*
  $apcLoader = new Symfony\Component\ClassLoader\ApcClassLoader('mozart', $loader);
  $loader->unregister();
  $apcLoader->register(true);
 */

$environment = 'prod';
$debug = false;

if (defined( 'WP_DEBUG' ) && WP_DEBUG) {
	$environment = 'dev';
	$debug = true;

	Symfony\Component\Debug\Debug::enable( 1 );
}

require_once __DIR__ . '/MozartKernel.php';
//require_once __DIR__.'/MozartCache.php';

$kernel = new MozartKernel( $environment, $debug );
$kernel->loadClassCache();
//$kernel = new MozartCache($kernel);
// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();

$kernel->boot();
$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();

$requestStack = new Symfony\Component\HttpFoundation\RequestStack();
$requestStack->push( $request );

$kernel->getContainer()->enterScope( 'request' );
$kernel->getContainer()->set( 'request', $request, 'request' );

$request->setSession( $kernel->getContainer()->get( 'session' ) );
$kernel->getContainer()->set( 'request_stack', $requestStack );

Mozart::setContainer( $kernel->getContainer() );

$url = content_url( '/mozart/public/' );

preg_match( '%([^:]*):\/\/([^\/]*)(\/?.*)%', $url, $matches );
if (count( $matches ) == 4) {
	$context = $kernel->getContainer()->get( 'router' )->getContext();
	$context->setHost( $matches[2] );
	$context->setScheme( $matches[1] );
	$context->setBaseUrl( $matches[3] );
}

add_action(
	'wp_loader',
	function () {
		Mozart::service( 'kernel' )->shutdown();
	},
	999
);
