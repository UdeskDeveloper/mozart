<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

// $loader->add((string) wp_get_theme(), get_template_directory() . '/libraries');
// @todo: chage this hardcoded thing
$loader->add( 'Immobilier', __DIR__ . '/../../../themes/immobilier/libraries');

return $loader;
