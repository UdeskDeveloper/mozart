<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PluginBundle\Model;

use Mozart\Component\Plugin\PluginInterface;
use Mozart\Component\Plugin\PluginManagerInterface;

/**
 * Class PluginManager
 * @package Mozart\Bundle\PluginBundle
 */
class PluginManager implements PluginManagerInterface
{
    /**
     * @var PluginInterface[]
     */
    private $plugins = array();

    /**
     *
     */
    public function __construct()
    {
        $this->plugins = array();
    }

    /**
     * @return PluginInterface[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @param $slug
     * @return PluginInterface
     */
    public function getPlugin($slug)
    {
        return $this->plugins[$slug];
    }

    /**
     * Add individual plugin to our collection of plugins.
     *
     * If the required keys are not set or the plugin has already
     * been registered, the plugin is not added.
     *
     * @param PluginInterface $plugin
     */
    public function registerPlugin(PluginInterface $plugin)
    {

        if ($plugin->getSlug() === '' || $plugin->getName() === '') {
            return;
        }

        $this->plugins[$plugin->getSlug()] = $plugin;
        $this->sortByName();

    }

    /**
     *
     */
    private function sortByName()
    {
        $sorted = array();

        foreach ($this->plugins as $plugin) {
            $sorted[] = $plugin->getName();
        }

        array_multisort( $sorted, SORT_ASC, $this->plugins );
    }

    /**
     * @return array
     */
    public function getInactivePlugins()
    {
        $inactivePlugins = array();

        foreach ($this->plugins as $plugin) {
            if (is_plugin_inactive($plugin->getBasename())) {
                $inactivePlugins[$plugin->getSlug()] = $plugin;
            }
        }

        return $inactivePlugins;
    }

}
