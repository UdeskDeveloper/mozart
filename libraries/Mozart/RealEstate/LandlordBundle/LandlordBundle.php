<?php

namespace Mozart\RealEstate\LandlordBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LandlordBundle extends Bundle
{
    private static $post_type = 'landlord';

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public function boot()
    {
        if (\Mozart::isWpRunning() === false) {
            return;
        }

        add_action('init', array(&$this, 'registerPostTypes'));
        add_action('before_property_options_fields', array(&$this, 'add_fields_to_property_edit_page'), 5);
    }

    public function add_fields_to_property_edit_page($mb)
    {
        ?>
        <tr>
            <th>
                <label><?php echo __('Landlord', 'mozart'); ?></label>
            </th>
            <td>
        <?php $mb->the_field('landlord'); ?>

                <select name="<?php $mb->the_name(); ?>">
                    <option value="">---</option>
        <?php foreach ($this->container->get('realestate.landlord.model')->get_list() as $landlord): ?>
                        <option value="<?php echo $landlord->ID; ?>" <?php $mb->the_select_state($landlord->ID); ?>><?php echo $landlord->post_title ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <?php
    }

    /**
     * Custom post type
     */
    public function registerPostTypes()
    {
        $labels = array(
            'name' => __('Landlords', 'mozart'),
            'singular_name' => __('Landlord', 'mozart'),
            'add_new' => __('Add New', 'mozart'),
            'add_new_item' => __('Add New Landlord', 'mozart'),
            'edit_item' => __('Edit Landlord', 'mozart'),
            'new_item' => __('New Landlord', 'mozart'),
            'all_items' => __('All Landlords', 'mozart'),
            'view_item' => __('View Landlord', 'mozart'),
            'search_items' => __('Search Landlord', 'mozart'),
            'not_found' => __('No landlords found', 'mozart'),
            'not_found_in_trash' => __('No landlords found in Trash', 'mozart'),
            'parent_item_colon' => '',
            'menu_name' => __('Landlords', 'mozart'),
        );

        register_post_type(self::$post_type, array(
            'labels' => $labels,
            'supports' => array('title', 'editor'),
            'public' => false,
            'show_ui' => true,
            'rewrite' => false,
            'menu_position' => 32,
            'menu_icon' => \Mozart::parameter('wp.content.uri') . '/mozart' . '/public/bundles/landlord/img/landlords.png',
                )
        );

        $this->register_metabox();
    }

    private function register_metabox()
    {
        /**
         * Meta options for custom post type
         */
        $landlord_metabox = new \wpalchemy_MetaBox(array(
            'id' => '_landlord_meta',
            'title' => __('Landlord', 'mozart'),
            'template' => __DIR__ . '/meta.php',
            'types' => array(self::$post_type),
            'prefix' => '_landlord_',
            'mode' => WPALCHEMY_MODE_EXTRACT,
        ));
    }

}
