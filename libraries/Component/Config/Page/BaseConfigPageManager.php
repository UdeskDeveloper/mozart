<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config\Page;


class BaseConfigPageManager extends AbstractConfigPageManager
{
    public function registerPage(ConfigPageInterface $configPage)
    {
        $this->pages[$configPage->getKey()] = $configPage;
    }

    protected function lazyPreparePage(ConfigPageInterface $configPage)
    {
        return array(
            'name'      => translate( $configPage->getName() ),
            'shortname' => translate($configPage->getShortName()),
            'key'       => translate( $configPage->getKey() ),
            'user_role' => $configPage->getUserCapabilities(),
            'parent'    => $configPage->getParent(),
            'position'  => $configPage->getMenuPosition(),
            'icon'      => $configPage->getIconUrl(),
            'redirect'  => $configPage->toRedirect()
        );
    }

    public function registerPages()
    {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        foreach ($this->getPages() as $key => $page) {
            $page = $this->lazyPreparePage( $page );

            if (empty( $page['parent'] )) {

                // add page
                add_menu_page(
                    $page['name'],
                    $page['shortname'],
                    $page['user_role'],
                    $page['key'],
                    array( $this, 'displayPageCode' ),
                    $page['icon'],
                    $page['position']
                );

            } else {

                // add page
                add_submenu_page(
                    $page['parent'],
                    $page['name'],
                    $page['shortname'],
                    $page['user_role'],
                    $page['key'],
                    array( $this, 'displayPageCode' )
                );

            }
        }
    }

    public function displayPageCode()
    {
        echo 'Please install Mozart Forms to render this page\'s fields';
    }
} 