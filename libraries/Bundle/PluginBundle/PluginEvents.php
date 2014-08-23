<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PluginBundle;

final class PluginEvents
{
    const BEFORE_ACTIVATION = 'activate_plugin';
    const AFTER_ACTIVATION = 'activated_plugin';

    const BEFORE_DEACTIVATION = 'deactivate_plugin';
    const AFTER_DEACTIVATION = 'deactivated_plugin';

    const UNINSTALL = 'wordpress.plugin_uninstall';
}
