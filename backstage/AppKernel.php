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
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            // load core modules
            new Mozart\Bundle\NucleusBundle\MozartNucleusBundle(),
            new Mozart\Bundle\ShortcodeBundle\MozartShortcodeBundle(),
            new Mozart\Bundle\TaxonomyBundle\MozartTaxonomyBundle(),
            new Mozart\Bundle\UserBundle\MozartUserBundle(),
            // load admin modules
            new Mozart\Bundle\BackofficeBundle\MozartBackofficeBundle(),

            // load UI components
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            new Mozart\UI\WebIconBundle\MozartWebIconBundle()
        );

        $bundles = apply_filters( 'register_mozart_bundle', $bundles );

        if (in_array( $this->getEnvironment(), array( 'dev', 'test' ) )) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
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

        if (isset( $themeConfig['scripts']['pre-initialize-mozart-bundles'] )) {
            foreach ((array)$themeConfig['scripts']['pre-initialize-mozart-bundles'] as $script) {
                call_user_func($script);
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
        if (\Mozart::isWpRunning() === true) {
            return WP_CONTENT_DIR . '/mozart/storage/cache/' . $this->environment;
        } else {
            return $this->getRootDir() . '/../../../mozart/storage/cache/' . $this->environment;
        }
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        if (\Mozart::isWpRunning() === true) {
            return WP_CONTENT_DIR . '/mozart/storage/logs';
        } else {
            return $this->getRootDir() . '/../../../mozart/storage/logs';
        }
    }

    /**
     * @return string|void
     */
    public function getRootDirUri()
    {
        if (\Mozart::isWpRunning() === true) {
            if (0 === strpos( $this->getRootDir(), WP_CONTENT_DIR )) {
                return content_url( str_replace( WP_CONTENT_DIR, '', $this->getRootDir() ) );
            } elseif (0 === strpos( $this->getRootDir(), ABSPATH )) {
                return site_url( str_replace( ABSPATH, '', $this->getRootDir() ) );
            } elseif (0 === strpos( $this->getRootDir(), WP_PLUGIN_DIR ) || 0 === strpos(
                    $this->getRootDir(),
                    WPMU_PLUGIN_DIR
                )
            ) {
                return plugins_url( basename( $this->getRootDir() ), $this->getRootDir() );
            }
        }

        return '';
    }
}
