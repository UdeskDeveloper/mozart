<?php

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Liip\ThemeBundle\LiipThemeBundle;
use Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle;
use Mozart\Bundle\BlogBundle\MozartBlogBundle;
use Mozart\Bundle\CacheBundle\MozartCacheBundle;
use Mozart\Bundle\CommentBundle\MozartCommentBundle;
use Mozart\Bundle\FieldBundle\MozartFieldBundle;
use Mozart\Bundle\MediaBundle\MozartMediaBundle;
use Mozart\Bundle\MenuBundle\MozartMenuBundle;
use Mozart\Bundle\NucleusBundle\MozartNucleusBundle;
use Mozart\Bundle\OptionBundle\MozartOptionBundle;
use Mozart\Bundle\PostBundle\MozartPostBundle;
use Mozart\Bundle\ShortcodeBundle\MozartShortcodeBundle;
use Mozart\Bundle\TaxonomyBundle\MozartTaxonomyBundle;
use Mozart\Bundle\ThemeBundle\MozartThemeBundle;
use Mozart\Bundle\UserBundle\MozartUserBundle;
use Mozart\Bundle\WidgetBundle\MozartWidgetBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class MozartKernel
 */
class MozartKernel extends Kernel
{

    /**
     * @param string $environment
     * @param bool $debug
     */
    public function __construct( $environment, $debug )
    {
        parent::__construct( $environment, $debug );
    }

    /**
     * @return array|mixed|void
     */
    public function registerBundles()
    {
        $bundles = array(
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new MonologBundle(),
            new DoctrineBundle(),
            new SensioFrameworkExtraBundle(),
            new LiipThemeBundle(),
            // load core modules
            new MozartNucleusBundle(),
            new MozartBlogBundle(),
            new MozartCacheBundle(),
            new MozartCommentBundle(),
            new MozartFieldBundle(),
            new MozartMediaBundle(),
            new MozartMenuBundle(),
            new MozartOptionBundle(),
            new MozartPostBundle(),
            new MozartShortcodeBundle(),
            new MozartTaxonomyBundle(),
            new MozartThemeBundle(),
            new MozartUserBundle(),
            new MozartWidgetBundle(),
            // load UI components
            new MopaBootstrapBundle()
        );

        $bundles = \Mozart::registerAdditionalBundles( $bundles );

        if (in_array( $this->getEnvironment(), array( 'dev', 'test' ) )) {
            $bundles[] = new WebProfilerBundle();
            $bundles[] = new SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function boot()
    {
        $this->bootWordpress();

        parent::boot();

        $request = Request::createFromGlobals();

        $requestStack = new RequestStack();
        $requestStack->push( $request );

        $this->container->enterScope( 'request' );
        $this->container->set( 'request', $request, 'request' );

        $request->setSession( $this->container->get( 'session' ) );
        $this->container->set( 'request_stack', $requestStack );
    }

    public function bootWordpress()
    {
        if (false === defined( 'ABSPATH' )) {

            define( 'WP_USE_THEMES', false );

            // let's find wp-load.php
            $finder = new Finder();

            $finder->files()
                ->name( 'wp-load.php' )
                ->ignoreUnreadableDirs()
                ->depth( '== 6' )
                ->in( __DIR__ . '/../../../../../../../../' );

            foreach ($finder as $file) {
                require_once $file->getRealpath();
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }
        }
    }

    /**
     * @param LoaderInterface $loader
     */
    public function registerContainerConfiguration( LoaderInterface $loader )
    {
        $loader->load( __DIR__ . '/config/config_' . $this->getEnvironment() . '.yml' );
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return WP_CONTENT_DIR . '/Mozart/Cache/' . $this->environment;
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return WP_CONTENT_DIR . '/Mozart/Logs';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mozart';
    }
}
