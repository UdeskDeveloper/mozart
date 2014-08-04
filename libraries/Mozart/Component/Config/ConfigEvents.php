<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config;

/**
 * Defines events for the configuration system.
 */
final class ConfigEvents
{
    /**
     * Name of event fired when saving the configuration object.
     *
     * @see \Drupal\Core\Config\Config::save()
     * @see \Drupal\Core\Config\ConfigFactory::onConfigSave()
     */
    const SAVE = 'config.save';

    /**
     * Name of event fired when deleting the configuration object.
     *
     * @see \Drupal\Core\Config\Config::delete()
     */
    const DELETE = 'config.delete';

    /**
     * Name of event fired when renaming a configuration object.
     *
     * @see \Drupal\Core\Config\ConfigFactoryInterface::rename().
     */
    const RENAME = 'config.rename';
}
