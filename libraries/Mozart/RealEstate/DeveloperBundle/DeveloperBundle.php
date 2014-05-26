<?php

namespace Mozart\RealEstate\DeveloperBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DeveloperBundle extends Bundle
{

    public static $post_type = 'developer';
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
        self::$slug = __('developers', 'mozart');
        add_action('init', array($this, 'registerPostTypes'));
        add_action('before_property_options_fields', array($this, 'add_fields_to_property_edit_page'), 2);
        add_action('widgets_init', array($this, 'registerWidgets'));
        add_action('onSaveProperty', array($this, 'updatePropertyCounter'));
    }

    public function updatePropertyCounter($property_id)
    {
        $developers = get_post_meta($property_id, '_property_developers', true);

        foreach ($developers as $developer_id) {
            $this->container
                    ->get('realestate.developer.model')
                    ->update_properties_counter($developer_id);
        }
    }

    public function registerWidgets()
    {
        register_widget('\Mozart\RealEstate\DeveloperBundle\Widget\Developer');
        register_widget('\Mozart\RealEstate\DeveloperBundle\Widget\AssignedDeveloper');
    }

    public function add_fields_to_property_edit_page($mb)
    {
        ?>
        <tr>
            <th>
                <label><?php echo __('Developers', 'mozart'); ?></label>
            </th>
            <td>
                <?php $mb->the_field('developers', WPALCHEMY_FIELD_HINT_SELECT_MULTI); ?>

                <select multiple="multiple" name="<?php $mb->the_name(); ?>">
                    <?php foreach ($this->container->get('realestate.developer.model')->get(9999) as $developer): ?>
                        <option value="<?php echo $developer->ID; ?>" <?php $mb->the_select_state($developer->ID); ?>><?php echo $developer->post_title ?></option>
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
            'name' => __('Developers', 'mozart'),
            'singular_name' => __('Developer', 'mozart'),
            'add_new' => __('Add New', 'mozart'),
            'add_new_item' => __('Add New Developer', 'mozart'),
            'edit_item' => __('Edit Developer', 'mozart'),
            'new_item' => __('New Developer', 'mozart'),
            'all_items' => __('All Developers', 'mozart'),
            'view_item' => __('View Developer', 'mozart'),
            'search_items' => __('Search Developer', 'mozart'),
            'not_found' => __('No developers found', 'mozart'),
            'not_found_in_trash' => __('No developers found in Trash', 'mozart'),
            'parent_item_colon' => '',
            'menu_name' => __('Developers', 'mozart'),
        );

        register_post_type(self::$post_type, array(
            'labels' => $labels,
            'rewrite' => array(
                'slug' => self::$slug,
            ),
            'hierarchical' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields'),
            'public' => true,
            'has_archive' => true,
            'menu_position' => 32,
            'menu_icon' => $this->container->getParameter('wp.content.uri') . '/mozart' . '/public/bundles/developer/img/developers.png',
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
            'id' => '_developer_meta',
            'title' => __('Developer Options', 'mozart'),
            'template' => __DIR__ . '/meta.php',
            'types' => array(self::$post_type),
            'prefix' => '_developer_',
            'mode' => WPALCHEMY_MODE_EXTRACT,
                )
        );
    }

}
