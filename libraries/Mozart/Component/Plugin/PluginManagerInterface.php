<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Plugin;

interface PluginManagerInterface
{
    public function getInstaller();

    public function getActivator();

    public function getPlugins();
} 