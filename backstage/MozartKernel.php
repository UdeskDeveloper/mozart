<?php

class MozartKernel extends Symfony\Component\HttpKernel\Kernel
{
	/**
	 * @param string $environment
	 * @param bool   $debug
	 */
	public function __construct($environment, $debug)
	{
		parent::__construct( $environment, $debug );

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
			array( $this, 'shutdown' ),
			999
		);

	}

	/**
	 * @return array|mixed|void
	 */
	public function registerBundles()
	{
		$bundles = array(
			new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new Symfony\Bundle\TwigBundle\TwigBundle(),
			new Symfony\Bundle\MonologBundle\MonologBundle(),
			new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
			new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
			new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
			new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
			new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
			new Liip\ThemeBundle\LiipThemeBundle(),
			// load core modules
			new Mozart\Bundle\NucleusBundle\MozartNucleusBundle(),
			new Mozart\Bundle\ActionBundle\MozartActionBundle(),
			new Mozart\Bundle\AdminBundle\MozartAdminBundle(),
			new Mozart\Bundle\AjaxBundle\MozartAjaxBundle(),
			new Mozart\Bundle\BlogBundle\MozartBlogBundle(),
			new Mozart\Bundle\BuilderBundle\MozartBuilderBundle(),
			new Mozart\Bundle\CacheBundle\MozartCacheBundle(),
			new Mozart\Bundle\CommentBundle\MozartCommentBundle(),
			new Mozart\Bundle\ConfigBundle\MozartConfigBundle(),
			new Mozart\Bundle\MediaBundle\MozartMediaBundle(),
			new Mozart\Bundle\MenuBundle\MozartMenuBundle(),
			new Mozart\Bundle\PluginBundle\MozartPluginBundle(),
			new Mozart\Bundle\PostBundle\MozartPostBundle(),
			new Mozart\Bundle\ShortcodeBundle\MozartShortcodeBundle(),
			new Mozart\Bundle\TaxonomyBundle\MozartTaxonomyBundle(),
			new Mozart\Bundle\ThemeBundle\MozartThemeBundle(),
			new Mozart\Bundle\UserBundle\MozartUserBundle(),
			new Mozart\Bundle\WidgetBundle\MozartWidgetBundle(),
			// load UI components
			new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle()
		);

		$bundles = \Mozart::registerAdditionalBundles( $bundles );

		if (in_array( $this->getEnvironment(), array( 'dev', 'test' ) )) {
			$bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
			$bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
			$bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
			$bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
		}

		return $bundles;
	}

	public function boot()
	{
		$this->bootWordpress();
		$this->loadThemeBundles();

		parent::boot();

		$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();

		$requestStack = new Symfony\Component\HttpFoundation\RequestStack();
		$requestStack->push( $request );

		$this->container->enterScope( 'request' );
		$this->container->set( 'request', $request, 'request' );

		$request->setSession( $this->container->get( 'session' ) );
		$this->container->set( 'request_stack', $requestStack );
	}

	/**
	 * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
	 */
	public function registerContainerConfiguration(Symfony\Component\Config\Loader\LoaderInterface $loader)
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

	protected function getContainerClass()
	{
		return 'Mozart' . ( $this->debug ? 'Naked' : '' ) . 'Orchestra';
	}

	protected function bootWordpress()
	{
		if (false === defined( 'ABSPATH' )) {

			define( 'WP_USE_THEMES', false );

			// let's find wp-load.php
			$finder = new Symfony\Component\Finder\Finder();

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

		add_action( 'deactivate_plugin', array( $this, 'clearCache' ), 10, 2 );
		add_action( 'activate_plugin', array( $this, 'clearCache' ), 10, 2 );
		add_action( "after_switch_theme", array( $this, 'clearCache' ), 10, 2 );
		add_action( "switch_theme", array( $this, 'clearCache' ), 10, 2 );
	}

	protected function loadThemeBundles()
	{
		$theme = new Mozart\Bridge\WordPress\Theme( $GLOBALS['wp_theme_directories'] );
		if (false === file_exists( $theme->getTemplateDirectoryPath() . '/backstage/bootstrap.php' )) {
			return;
//            throw new FileNotFoundException( '/backstage/bootstrap.php was not found in your theme' );
		}
		include $theme->getTemplateDirectoryPath() . '/backstage/bootstrap.php';
	}

	public function clearCache()
	{
		$filesystem = new Symfony\Component\Filesystem\Filesystem();

		if ($filesystem->exists( $this->container->getParameter( 'kernel.cache_dir' ) )) {
			$filesystem->remove( $this->container->getParameter( 'kernel.cache_dir' ) );
		}

		flush_rewrite_rules();
	}
}
