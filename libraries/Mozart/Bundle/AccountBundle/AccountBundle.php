<?php

namespace Mozart\Bundle\AccountBundle;

use Mozart\Component\Bundle\Bundle;

class AccountBundle extends Bundle
{
    public function addActions()
    {
        add_action('widgets_init', array($this, 'registerWidgets'));
    }

    public function addFilters()
    {
        add_filter('site_url', array($this, 'changeLoginUrl'));
        add_filter('logout_url', array($this, 'changeLogoutUrl'));
    }

    public function changeLoginUrl($url)
    {
        if (strpos($url, 'wp-login.php') !== false) {
                $url = str_replace('wp-login.php', 'my-account', $url);
        }

        return $url;
    }

    public function changeLogoutUrl()
    {
        return home_url('logout');
    }

    public function registerWidgets()
    {
        register_widget('\Mozart\Bundle\AccountBundle\Widget\Login');
        register_widget('\Mozart\Bundle\AccountBundle\Widget\Register');
    }

}
