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
     */
    const SAVE = 'config.save';

    /**
     * Name of event fired when deleting the configuration object.
     */
    const DELETE = 'config.delete';

    /**
     * Name of event fired when renaming a configuration object.
     */
    const RENAME = 'config.rename';

    /**
     * Name of event fired when validating in the configuration import process.
     */
    const IMPORT_VALIDATE = 'config.importer.validate';

    /**
     * Name of event fired when when importing configuration to target storage.
     *
     */
    const IMPORT = 'config.importer.import';

    /**
     * Name of event fired to collect information on all collections.
     */
    const COLLECTION_INFO = 'config.collection_info';

}
