<?php

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
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
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class MozartKernel
 */
class MozartKernel extends Kernel
{

	/**
	 * @param string $environment
	 * @param bool   $debug
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

		$bundles = apply_filters( 'register_mozart_bundle', $bundles );

		if (in_array( $this->getEnvironment(), array( 'dev', 'test' ) )) {
			$bundles[] = new WebProfilerBundle();
			$bundles[] = new SensioGeneratorBundle();
		}

		return $bundles;
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
