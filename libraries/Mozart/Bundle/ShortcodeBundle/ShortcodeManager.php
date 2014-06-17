<?php

namespace Mozart\Bundle\ShortcodeBundle;

class ShortcodeManager
{
    public function __construct()
    {
        add_action('init', array($this, 'addButtonsToEditor'));
    }

    public function addButtonsToEditor()
    {
        add_filter('mce_external_plugins', array($this, 'mce_external_plugins'));
        add_filter('mce_buttons_3', array($this, 'register_buttons'));
    }

    public function mce_external_plugins($plugin_array)
    {
        $plugin_array['mozart'] = \Mozart::parameter('wp.content.uri') . '/mozart' . '/public/bundles/mozartnucleus/shortcodes/js/shortcodes.js';

        return $plugin_array;
    }

    public function register_buttons($buttons)
    {
        array_push($buttons, 'row');
        array_push($buttons, 'span3');
        array_push($buttons, 'span4');
        array_push($buttons, 'span6');
        array_push($buttons, 'span8');
        array_push($buttons, 'span9');
        array_push($buttons, 'content_box');
        array_push($buttons, 'faq');
        array_push($buttons, 'pricing');

        return $buttons;
    }
}
