<?php

namespace Mozart\RealEstate\PartnerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PartnerBundle extends Bundle
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
        add_action('widgets_init', array(&$this, 'registerWidgets'));
    }

    public function registerWidgets()
    {
        register_widget('\Mozart\RealEstate\PartnerBundle\Widget\Partners');
    }

    /**
     * Custom post type
     */
    public function registerPostTypes()
    {
        $labels = array(
            'name' => __('Partners', 'mozart'),
            'singular_name' => __('Partner', 'mozart'),
            'add_new' => __('Add New', 'mozart'),
            'add_new_item' => __('Add New Partner', 'mozart'),
            'edit_item' => __('Edit Partner', 'mozart'),
            'new_item' => __('New Partner', 'mozart'),
            'all_items' => __('All Partners', 'mozart'),
            'view_item' => __('View Partner', 'mozart'),
            'search_items' => __('Search Partner', 'mozart'),
            'not_found' => __('No Partners found', 'mozart'),
            'not_found_in_trash' => __('No Partners found in Trash', 'mozart'),
            'parent_item_colon' => '',
            'menu_name' => __('Partners', 'mozart'),
        );

        register_post_type('partner', array(
            'labels' => $labels,
            'supports' => array('title', 'thumbnail',),
            'public' => false,
            'show_ui' => true,
            'exclude_from_search' => true,
            'show_in_nav_menus' => false,
            'menu_position' => 32,
            'menu_icon' => \Mozart::parameter('wp.content.uri') . '/mozart' . '/public/bundles/partner/img/partners.png',
                )
        );
        $this->registerMetabox();
    }

    private function registerMetabox()
    {
        /**
         * Meta options for custom post type
         */
        $partner_metabox = new \wpalchemy_MetaBox(array(
            'id' => '_partner_meta',
            'title' => __('Partner Options', 'mozart'),
            'template' => __DIR__ . '/meta.php',
            'types' => array('partner',),
            'prefix' => '_partner_',
            'mode' => WPALCHEMY_MODE_EXTRACT,
        ));
    }

}
