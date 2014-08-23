<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PluginBundle;

class PluginEvents
{
    const PLUGIN_ACTIVATION = 'wordpress.plugin_activation';
    const PLUGIN_DEACTIVATION = 'wordpress.plugin_deactivation';
    const PLUGIN_UNINSTALL = 'wordpress.plugin_uninstall';
}
