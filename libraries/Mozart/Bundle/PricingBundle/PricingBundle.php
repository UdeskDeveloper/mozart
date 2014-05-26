<?php

namespace Mozart\Bundle\PricingBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mozart\Bundle\NucleusBundle\Shortcode\ShortcodableInterface;

class PricingBundle extends Bundle implements ShortcodableInterface
{
    private static $post_type = 'pricing';

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public function boot()
    {
        if (!defined('ABSPATH')) {
            return;
        }
        add_action('init', array($this, 'registerPostTypes'));
        add_action('init', array($this, 'addShortcodes'));
    }

    public function addShortcodes()
    {
        add_shortcode('pricing', array('\Mozart\Bundle\PricingBundle\Shortcode\Pricing', 'shortcode'));
    }

    public function registerPostTypes()
    {
        $labels = array(
            'name' => __('Pricings', 'mozart'),
            'singular_name' => __('Pricing', 'mozart'),
            'add_new' => __('Add New', 'mozart'),
            'add_new_item' => __('Add New Pricing', 'mozart'),
            'edit_item' => __('Edit Pricing', 'mozart'),
            'new_item' => __('New Pricing', 'mozart'),
            'all_items' => __('All Pricings', 'mozart'),
            'view_item' => __('View Pricing', 'mozart'),
            'search_items' => __('Search Pricing', 'mozart'),
            'not_found' => __('No Pricings found', 'mozart'),
            'not_found_in_trash' => __('No Pricings found in Trash', 'mozart'),
            'parent_item_colon' => '',
            'menu_name' => __('Pricings', 'mozart'),
        );

        register_post_type(self::$post_type, array(
            'labels' => $labels,
            'supports' => array('title', 'editor'),
            'public' => false,
            'show_ui' => true,
            'publicly_queryable' => true,
            'hierarchical' => false,
            'exclude_from_search' => true,
            'show_in_nav_menus' => false,
            'categories' => array(),
            'menu_position' => 32,
            'menu_icon' => \Mozart::parameter('wp.content.uri') . '/mozart' . '/public/bundles/pricing/img/pricing.png',
                )
        );
    }

}
