<?php

namespace Mozart\RealEstate\AgencyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AgencyBundle extends Bundle
{

    public static $post_type = 'agency';
    public static $slug;

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public function boot()
    {
        if (!defined('ABSPATH')) {
            return;
        }
        self::$slug = __('agencies', 'mozart');
        add_action('init', array($this, 'registerPostTypes'));

        add_action('before_property_options_fields', array($this, 'add_fields_to_property_edit_page'), 2);
        add_action('before_agent_options_fields', array($this, 'add_fields_to_agent_edit_page'), 2);

        add_action('widgets_init', array($this, 'registerWidgets'));
        add_action('onSaveProperty', array($this, 'updatePropertyCounter'));
    }

    public function updatePropertyCounter($property_id)
    {
        $agencies = get_post_meta($property_id, '_property_agencies', true);

        foreach ($agencies as $agency_id) {
            $this->container
                    ->get('realestate.agency.model')
                    ->update_properties_counter($agency_id);
        }
    }

    public function registerWidgets()
    {
        register_widget('\Mozart\RealEstate\AgencyBundle\Widget\Agency');
        register_widget('\Mozart\RealEstate\AgencyBundle\Widget\AssignedAgency');
    }

    public function add_fields_to_agent_edit_page($mb)
    {
        ?>
        <tr>
            <th>
                <label><?php print __('Agency', 'mozart'); ?></label>
            </th>
            <td>
        <?php $mb->the_field('agency'); ?>

                <select name="<?php $mb->the_name(); ?>">
                    <option value="">---</option>
                    <?php foreach ($this->container->get('realestate.agency.model')->get() as $agency): ?>
                        <option value="<?php echo $agency->ID; ?>" <?php $mb->the_select_state($agency->ID); ?>><?php echo $agency->post_title ?></option>
        <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <?php
    }

    public function add_fields_to_property_edit_page($mb)
    {
        ?>
        <tr>
            <th>
                <label><?php echo __('Agencies', 'mozart'); ?></label>
            </th>
            <td>
        <?php $mb->the_field('agencies', WPALCHEMY_FIELD_HINT_SELECT_MULTI); ?>

                <select multiple="multiple" name="<?php $mb->the_name(); ?>">
                    <?php foreach ($this->container->get('realestate.agency.model')->get(9999) as $agency): ?>
                        <option value="<?php echo $agency->ID; ?>" <?php $mb->the_select_state($agency->ID); ?>><?php echo $agency->post_title ?></option>
        <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <?php
    }

    /**
     * Creates agent custom post type
     */
    public function registerPostTypes()
    {
        $labels = array(
            'name' => __('Agencies', 'mozart'),
            'singular_name' => __('Agency', 'mozart'),
            'add_new' => __('Add New', 'mozart'),
            'add_new_item' => __('Add New Agency', 'mozart'),
            'edit_item' => __('Edit Agency', 'mozart'),
            'new_item' => __('New Agency', 'mozart'),
            'all_items' => __('All Agencies', 'mozart'),
            'view_item' => __('View Agency', 'mozart'),
            'search_items' => __('Search Agency', 'mozart'),
            'not_found' => __('No agencies found', 'mozart'),
            'not_found_in_trash' => __('No agencies found in Trash', 'mozart'),
            'parent_item_colon' => '',
            'menu_name' => __('Agencies', 'mozart'),
        );

        register_post_type(self::$post_type, array(
            'labels' => $labels,
            'rewrite' => array(
                'slug' => self::$slug,
            ),
            'hierarchical' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
            'public' => true,
            'has_archive' => true,
            'menu_position' => 32,
            'menu_icon' => \Mozart::parameter('wp.content.uri') . '/mozart' . '/public/bundles/agency/img/agencies.png',
                )
        );

        $this->register_metabox();
    }

    private function register_metabox()
    {
        /**
         * Meta options for custom post type
         */
        $agent_metabox = new \wpalchemy_MetaBox(array(
            'id' => '_agency_meta',
            'title' => __('Agency Options', 'mozart'),
            'template' => __DIR__ . '/meta.php',
            'types' => array(self::$post_type),
            'prefix' => '_agency_',
            'mode' => WPALCHEMY_MODE_EXTRACT,
                )
        );
    }

}
