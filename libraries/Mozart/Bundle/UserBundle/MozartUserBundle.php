<?php

namespace Mozart\Bundle\UserBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class MozartUserBundle
 *
 * @package Mozart\Bundle\UserBundle
 */
class MozartUserBundle extends Bundle
{
    public function build( ContainerBuilder $container )
    {
        parent::build( $container );
    }

    public function boot()
    {
        add_action('widgets_init', array($this, 'registerWidgets'));

        // Filters
        add_filter('site_url', array($this, 'changeLoginUrl'));
        add_filter('logout_url', array($this, 'changeLogoutUrl'));
    }

    /**
     * @param $url
     *
     * @return mixed
     */
    public function changeLoginUrl($url)
    {
        if (strpos($url, 'wp-login.php') !== false) {
                $url = str_replace('wp-login.php', 'my-account', $url);
        }

        return $url;
    }

    /**
     * @return string|void
     */
    public function changeLogoutUrl()
    {
        return home_url('logout');
    }

    /**
     *
     */
    public function registerWidgets()
    {
        register_widget('\Mozart\Bundle\UserBundle\Widget\Login');
        register_widget('\Mozart\Bundle\UserBundle\Widget\Register');
    }

}
