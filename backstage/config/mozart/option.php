<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

$container->setParameter(
    "mozart.option",
    array(
        'opt_name'           => 'mozart',
        // This is where your data is stored in the database and also becomes your global variable name.
        'display_name'       => wp_get_theme()->get( 'Name' ),
        // Name that appears at the top of your panel
        'display_version'    => wp_get_theme()->get( 'Version' ),
        // Version that appears at the top of your panel
        'menu_type'          => 'menu',
        //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
        'allow_sub_menu'     => true,
        // Show the sections below the admin menu item or not
        'menu_title'         => ucfirst( wp_get_theme()->get( 'Name' ) ),
        'page_title'         => ucfirst( wp_get_theme()->get( 'Name' ) ) . ' Options',
        // You will need to generate a Google API key to use this feature.
        // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
        'google_api_key'     => '',
        // Must be defined to add google fonts to the typography module

        'async_typography'   => false,
        // Use a asynchronous font on the front end or font string
        'admin_bar'          => true,
        // Show the time the page took to load, etc
        'customizer'         => true,
        // Enable basic customizer support
        'open_expanded'      => false,
        // Allow you to start the panel in an expanded way initially.
        'disable_save_warn'  => true,
        // Disable the save warning when a user changes a field

        // Order where the menu appears in the admin area
        'page_priority'      => 50.2,
        // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
        'page_parent'        => 'themes.php',
        // Permissions needed to access the options panel.
        'page_permissions'   => 'manage_options',
        // Specify a custom URL to an icon
        'menu_icon'          => '',
        // Force your panel to always open to a specific tab (by id)
        'last_tab'           => '',
        'page_icon'          => 'icon-themes',
        // Icon displayed in the admin panel next to your menu_title
        'page_slug'          => 'mozart_options',
        // Page slug used to denote the panel
        'save_defaults'      => true,
        // On load save the defaults to DB before user clicks save or not
        'default_show'       => false,
        // If true, shows the default value next to each field that is not the default value.
        'default_mark'       => '',
        // What to print by the field's title if the value shown is default. Suggested: *
        'show_import_export' => true,
        // Shows the Import/Export panel when not used as a field.

        'transient_time'     => 60 * MINUTE_IN_SECONDS,
        // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
        'output'             => true,
        // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
        'output_tag'         => true,
        'footer_credit'      => '',
        'database'           => '',
        // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
        'system_info'        => false,
        // HINTS
        'hints'              => array(
            'icon'          => 'icon-question-sign',
            'icon_position' => 'right',
            'icon_color'    => 'lightgray',
            'icon_size'     => 'normal',
            'tip_style'     => array(
                'color'   => 'light',
                'shadow'  => true,
                'rounded' => false,
                'style'   => '',
            ),
            'tip_position'  => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect'    => array(
                'show' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'mouseover',
                ),
                'hide' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'click mouseleave',
                ),
            ),
        ),
        'intro_text'         => '',
        'footer_text'        => ''
    )
);