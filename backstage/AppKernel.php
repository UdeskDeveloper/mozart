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
    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);

        // $_SERVER['SYMFONY__KERNEL__THEME_DIR'] = get_template_directory();
        $_SERVER['SYMFONY__KERNEL__THEME_DIR'] = __DIR__ . '/../../../themes/immobilier';
        // $_SERVER['SYMFONY__KERNEL__THEME_DIR_URI'] = get_template_directory_uri();
        $_SERVER['SYMFONY__KERNEL__THEME_DIR_URI'] = 'http://amirassl.dev.rhetina.com/wp-content/themes/immobilier';
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
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            // load core modules
            new Mozart\Bundle\NucleusBundle\MozartNucleusBundle(),
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            // load admin modules
            new Mozart\Bundle\BackofficeBundle\MozartBackofficeBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
        );

        $bundles = apply_filters('register_mozart_bundle', $bundles);

        $bundles[] = new Immobilier\ThemeBundle\ImmobilierThemeBundle();

        // load theme components
        $bundles[] = new Mozart\Bundle\AccountBundle\AccountBundle();
        $bundles[] = new Mozart\Bundle\FaqBundle\FaqBundle();
        $bundles[] = new Mozart\Bundle\GoogleAnalyticsBundle\GoogleAnalyticsBundle();
        $bundles[] = new Mozart\Bundle\PricingBundle\PricingBundle();
        $bundles[] = new Mozart\Bundle\SocializeBundle\SocializeBundle();

        // load UI components
        $bundles[] = new \Mozart\UI\WebIconBundle\MozartWebIconBundle();

        // load real estate components
  //      $bundles[] = new Mozart\RealEstate\AgencyBundle\AgencyBundle();
        $bundles[] = new Mozart\RealEstate\AgentBundle\AgentBundle();
        $bundles[] = new Mozart\RealEstate\DeveloperBundle\DeveloperBundle();
        $bundles[] = new Mozart\RealEstate\LandlordBundle\LandlordBundle();
        $bundles[] = new Mozart\RealEstate\PropertyBundle\PropertyBundle();
        $bundles[] = new Mozart\RealEstate\PartnerBundle\PartnerBundle();

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    /**
     * @param LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
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
            if (0 === strpos($this->getRootDir(), WP_CONTENT_DIR)) {
                return content_url(str_replace(WP_CONTENT_DIR, '', $this->getRootDir()));
            } elseif (0 === strpos($this->getRootDir(), ABSPATH)) {
                return site_url(str_replace(ABSPATH, '', $this->getRootDir()));
            } elseif (0 === strpos($this->getRootDir(), WP_PLUGIN_DIR) || 0 === strpos($this->getRootDir(), WPMU_PLUGIN_DIR)) {
                return plugins_url(basename($this->getRootDir()), $this->getRootDir());
            }
        }
        return '';
    }

    /**
     *
     */
    public static function onActivation()
    {
        define('WP_USE_THEMES', false);

        $finder = new Symfony\Component\Finder\Finder();

        $finder->files()
                ->name('wp-load.php')
                ->ignoreUnreadableDirs()
                ->depth('== 0')
                ->in(__DIR__ . '/../../')
                ->in(__DIR__ . '/../../../')
                ->in(__DIR__ . '/../../../../')
                ->in(__DIR__ . '/../../../../../')
                ->in(__DIR__ . '/../../../../../../')
                ->in(__DIR__ . '/../../../../../../../')
                ->in(__DIR__ . '/../../../../../../../../');

        foreach ($finder as $file) {
            require_once( $file->getRealpath() );
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $wordressInfo = array(
            'rhetina_nucleus' => array(
                'wp' => array(
                    'home' => array(
                        'dir' => get_home_path(),
                        'uri' => get_home_url()
                    ),
                    'site' => array(
                        'uri' => get_site_url()
                    ),
                    'plugin' => array(
                        'dir' => WP_PLUGIN_DIR,
                        'uri' => plugins_url()
                    ),
                    'theme' => array(
                        'name' => (string) wp_get_theme(),
                        'dir' => get_template_directory(),
                        'uri' => get_template_directory_uri()
                    ),
                    'stylesheet' => array(
                        'dir' => get_stylesheet_directory(),
                        'uri' => get_stylesheet_directory_uri()
                    ),
                    'content' => array(
                        'dir' => WP_CONTENT_DIR,
                        'uri' => content_url()
                    ),
                    'includes' => array(
                        'dir' => WPINC,
                        'uri' => includes_url()
                    )
                )
            )
        );

        $dumper = new \Symfony\Component\Yaml\Dumper();
        $yaml = $dumper->dump($wordressInfo, 5);

        file_put_contents(__DIR__ . '/config/wordpress.yml', $yaml);
    }

}
