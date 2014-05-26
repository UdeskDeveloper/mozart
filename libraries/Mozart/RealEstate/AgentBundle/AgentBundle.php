<?php

namespace Mozart\RealEstate\AgentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AgentBundle extends Bundle
{
    private static $post_type = 'agent';
    private static $slug;

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public function boot()
    {
        if (\Mozart::isWpRunning() === false) {
            return;
        }

        self::$slug = __('agents', 'mozart');
        add_action('init', array($this, 'registerPostTypes'));
        add_action('init', array($this, 'modify_posts_per_agents_page'), 0);

        add_action('before_property_options_fields', array($this, 'add_fields_to_property_edit_page'), 1);

        add_action('widgets_init', array($this, 'registerWidgets'));
        add_action('onSaveProperty', array($this, 'updatePropertyCounter'));
    }

    public function updatePropertyCounter($property_id)
    {
        $agents = get_post_meta($property_id, '_property_developers', true);

        foreach ($agents as $agent_id) {
            $this->container
                    ->get('realestate.agent.model')
                    ->update_properties_counter($agent_id);
        }
    }

    public function registerWidgets()
    {
        register_widget('\Mozart\RealEstate\AgentBundle\Widget\Agents');
        register_widget('\Mozart\RealEstate\AgentBundle\Widget\AssignedAgents');
    }

    public function add_fields_to_property_edit_page($mb)
    {
        ?>
        <tr>
            <th>
                <label><?php echo __('Agents', 'mozart'); ?></label>
            </th>
            <td>
                <?php $mb->the_field('agents', WPALCHEMY_FIELD_HINT_SELECT_MULTI); ?>

                <select multiple="multiple" name="<?php $mb->the_name(); ?>">
                    <?php foreach ($this->container->get('realestate.agent.model')->get(9999) as $agent): ?>
                        <option value="<?php echo $agent->ID; ?>" <?php $mb->the_select_state($agent->ID); ?>><?php echo $agent->post_title ?></option>
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
            'name' => __('Agents', 'mozart'),
            'singular_name' => __('Agent', 'mozart'),
            'add_new' => __('Add New', 'mozart'),
            'add_new_item' => __('Add New Agent', 'mozart'),
            'edit_item' => __('Edit Agent', 'mozart'),
            'new_item' => __('New Agent', 'mozart'),
            'all_items' => __('All Agents', 'mozart'),
            'view_item' => __('View Agent', 'mozart'),
            'search_items' => __('Search Agent', 'mozart'),
            'not_found' => __('No agents found', 'mozart'),
            'not_found_in_trash' => __('No agents found in Trash', 'mozart'),
            'parent_item_colon' => '',
            'menu_name' => __('Agents', 'mozart'),
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
            'menu_icon' => \Mozart::parameter('wp.content.uri') . '/mozart' . '/public/bundles/agent/img/agents.png',
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
            'id' => '_agent_meta',
            'title' => __('Agent Options', 'mozart'),
            'template' => __DIR__ . '/meta.php',
            'types' => array(self::$post_type),
            'prefix' => '_agent_',
            'mode' => WPALCHEMY_MODE_EXTRACT,
        ));
    }

    /**
     * Change posts per page
     */
    public function modify_posts_per_agents_page()
    {
        add_filter('option_posts_per_page', array($this, 'option_posts_per_agents_page'));
    }

    public function option_posts_per_agents_page($value)
    {
        if (is_post_type_archive(self::$post_type)) {
            return parameter('agent', 'agents', 'per_page');
        }

        return $value;
    }

}
