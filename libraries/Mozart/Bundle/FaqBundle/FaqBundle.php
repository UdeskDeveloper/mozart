<?php

namespace Mozart\Bundle\FaqBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mozart\Bundle\NucleusBundle\Shortcode\ShortcodableInterface;

class FaqBundle extends Bundle implements ShortcodableInterface
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public function boot()
    {
        if (\Mozart::isWpRunning() === false) {
            return;
        }

        add_action('init', array($this, 'registerPostTypes'));
        add_action('init', array($this, 'registerTaxonomies'), 0);
        add_action('init', array($this, 'addShortcodes'), 0);
    }

    public function addShortcodes()
    {
        add_shortcode('faq', array('\Mozart\Bundle\FaqBundle\Shortcode\Faq', 'shortcode'));
    }

    /**
     * Custom post type
     */
    public function registerPostTypes()
    {
        $labels = array(
            'name' => __('FAQs', 'mozart'),
            'singular_name' => __('FAQ', 'mozart'),
            'add_new' => __('Add New', 'mozart'),
            'add_new_item' => __('Add New FAQ', 'mozart'),
            'edit_item' => __('Edit FAQ', 'mozart'),
            'new_item' => __('New FAQ', 'mozart'),
            'all_items' => __('All FAQs', 'mozart'),
            'view_item' => __('View FAQ', 'mozart'),
            'search_items' => __('Search FAQ', 'mozart'),
            'not_found' => __('No FAQs found', 'mozart'),
            'not_found_in_trash' => __('No FAQs found in Trash', 'mozart'),
            'parent_item_colon' => '',
            'menu_name' => __('FAQs', 'mozart'),
        );

        register_post_type('faq', array(
            'labels' => $labels,
            'supports' => array('title', 'editor',),
            'public' => true,
            'exclude_from_search' => true,
            'show_in_nav_menus' => false,
            'categories' => array('faq_categories',),
            'menu_position' => 32,
            'menu_icon' => \Mozart::parameter('wp.content.uri') . '/mozart' . '/public/bundles/faq/img/faq.png',
                )
        );
    }

    /**
     * Custom taxonomies
     */
    public function registerTaxonomies()
    {
        $categories_labels = array(
            'name' => __('Categories', 'mozart'),
            'singular_name' => __('Category', 'mozart'),
            'search_items' => __('Search Category', 'mozart'),
            'all_items' => __('All Categories', 'mozart'),
            'parent_item' => __('Parent Category', 'mozart'),
            'parent_item_colon' => __('Parent Category:', 'mozart'),
            'edit_item' => __('Edit Category', 'mozart'),
            'update_item' => __('Update Category', 'mozart'),
            'add_new_item' => __('Add New Category', 'mozart'),
            'new_item_name' => __('New Category', 'mozart'),
            'menu_name' => __('Category', 'mozart'),
        );

        register_taxonomy('faq_categories', 'faq', array(
            'labels' => $categories_labels,
            'hierarchical' => true,
            'query_var' => 'faq_categories',
            'rewrite' => 'faq_categories',
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
        ));
    }

}
