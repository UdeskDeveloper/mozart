<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config\Page;


class BaseConfigPageManager extends AbstractConfigPageManager
{
    public function registerPage(ConfigPageInterface $configPage)
    {
        $this->pages[$configPage->getKey()] = array(
            'name'      => $configPage->getName(),
            'key'       => $configPage->getKey(),
            'user_role' => $configPage->getUserCapabilities(),
            'parent'    => $configPage->getParent(),
            'position'  => $configPage->getMenuPosition(),
            'icon'      => $configPage->getIconUrl(),
            'redirect'  => $configPage->toRedirect()
        );
    }

} 