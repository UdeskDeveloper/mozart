<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PluginBundle;

use Mozart\Component\Plugin\Activator;
use Mozart\Component\Plugin\Installer;
use Mozart\Component\Plugin\PluginManagerInterface;

/**
 * Class PluginManager
 * @package Mozart\Bundle\PluginBundle
 */
class PluginManager implements PluginManagerInterface
{
    /**
     * @var Installer
     */
    private $installer;

    /**
     * @var Activator
     */
    private $activator;

    /**
     * @var array
     */
    private $options;

    private $plugins;

    /**
     * @param Installer $installer
     * @param Activator $activator
     */
    public function __construct( Installer $installer, Activator $activator, array $options = array() )
    {
        $this->activator = $activator;
        $this->installer = $installer;
        $this->setOptions( $options );
    }

    /**
     * @return Activator
     */
    public function getActivator()
    {
        return $this->activator;
    }

    /**
     * @return Installer
     */
    public function getInstaller()
    {
        return $this->installer;
    }

    /**
     * Amend default configuration settings.
     *
     * @param array $options Array of config options to pass as class properties.
     */
    public function setOptions( array $options )
    {
        $keys = array(
            'default_path',
            'has_notices',
            'dismissable',
            'dismiss_msg',
            'menu',
            'is_automatic',
            'message',
            'strings'
        );

        foreach ($keys as $key) {
            if (isset( $options[$key] )) {
                if (is_array( $options[$key] )) {
                    foreach ($options[$key] as $subkey => $value) {
                        $this->options[$key][$subkey] = $value;
                    }
                } else {
                    $this->options[$key] = $options[$key];
                }
            }
        }

    }

    /**
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @param array $plugins
     */
    public function setPlugins( $plugins )
    {
        $this->plugins = $plugins;
    }

    public function registerPlugin( $plugin )
    {
        $this->plugins[] = $plugin;
    }
}