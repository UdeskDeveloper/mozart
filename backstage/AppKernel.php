<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class AppKernel
 */
class AppKernel extends Kernel
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
     * @return string
     */
    public function getName()
    {
        return 'mozart';
    }

    /**
     * @return array|mixed|void
     */
    public function registerBundles()
    {
        $this->registerThemeMozartBundles();

        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            // load core modules
            new Mozart\Bundle\NucleusBundle\MozartNucleusBundle(),
            // load core modules responsible for WordPress entities
            new Mozart\Bundle\BlogBundle\MozartBlogBundle(),
            new Mozart\Bundle\CommentBundle\MozartCommentBundle(),
            new Mozart\Bundle\OptionBundle\MozartOptionBundle(),
            new Mozart\Bundle\PostBundle\MozartPostBundle(),
            new Mozart\Bundle\ShortcodeBundle\MozartShortcodeBundle(),
            new Mozart\Bundle\TaxonomyBundle\MozartTaxonomyBundle(),
            new Mozart\Bundle\ThemeBundle\MozartThemeBundle(),
            new Mozart\Bundle\UserBundle\MozartUserBundle(),
            new Mozart\Bundle\WidgetBundle\MozartWidgetBundle(),
            // load UI components
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            new Mozart\UI\WebIconBundle\MozartWebIconBundle()
        );

        $bundles = apply_filters( 'register_mozart_bundle', $bundles );

        if ( in_array( $this->getEnvironment(), array( 'dev', 'test' ) ) ) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        //    $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    /**
     * )
     */
    public function registerThemeMozartBundles()
    {
        // TODO: what if /themedir/vendor/autoload.php does not exist
        include_once get_template_directory() . '/vendor/autoload.php';

        // TODO: what if /themedir/composer.json does not exist
        $themeConfig = json_decode( file_get_contents( get_template_directory() . '/composer.json' ), true );

        if ( isset( $themeConfig['scripts']['pre-initialize-mozart-bundles'] ) ) {
            foreach ( (array)$themeConfig['scripts']['pre-initialize-mozart-bundles'] as $script ) {
                call_user_func( $script );
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
}
