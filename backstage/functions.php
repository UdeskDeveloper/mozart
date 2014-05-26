<?php

/**
 * @file
 * Functions that need to be loaded on every Rhetina request.
 */
function parameter($plugin_slug, $section_id, $option_id, $default = false)
{
    $key = $plugin_slug . '_' . $section_id . '_' . $option_id;

    return get_option(strtolower($key), $default);
}

function twiggy($view, array $parameters = array(), $display = true)
{
    if ($display) {
        echo \Mozart::service('templating')->render($view, $parameters);
    } else {
        return \Mozart::service('templating')->render($view, $parameters);
    }
}
