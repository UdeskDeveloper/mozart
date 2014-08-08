<?php

namespace  Mozart\Bundle\NucleusBundle\Security\Http\Logout;

use  Mozart\Bundle\NucleusBundle\Wordpress\ConfigurationManager;
use Symfony\Component\Security\Http\Logout\CookieClearingLogoutHandler;

class WordpressCookieClearingLogoutHandler extends CookieClearingLogoutHandler
{
    public function __construct(ConfigurationManager $configuration)
    {
        parent::__construct(array(
            $configuration->getLoggedInCookieName() => array(
                'path'   => $configuration->getCookiePath(),
                'domain' => $configuration->getCookieDomain(),
            )
        ));
    }
}
