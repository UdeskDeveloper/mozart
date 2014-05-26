<?php

namespace Mozart\RealEstate\PropertyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PropertyBundle extends Bundle
{

    private static $post_type = 'property';
    private static $domain = 'mozart';

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public function boot()
    {
        if (\Mozart::isWpRunning() === false) {
            return;
        }

        add_action('wp_enqueue_scripts', array($this, 'loadAssets'), 999);

        add_action('init', array($this, 'registerPostTypes'));
        add_action('init', array($this, 'registerTaxonomies'), 0);
        add_action('widgets_init', array($this, 'registerWidgets'));
        add_action('init', array($this, 'addShortcodes'), 0);
        add_action('init', array($this, 'processForms'));

        add_filter('manage_edit-property_columns', array($this, 'custom_post_columns'));
        add_action('manage_property_posts_custom_column', array($this, 'custom_post_manage'), 10, 2);
        add_action('init', array($this, 'modify_posts_per_properties_page'), 0);

        add_action('save_post', array($this, 'onSavePost'), 99);

        add_action('wp_ajax_properties_map_filter', array(
            $this->getPropertyController(),
            'ajaxPropertiesMapFilterAction'
        ));
        add_action('wp_ajax_nopriv_properties_map_filter', array(
            $this->getPropertyController(),
            'ajaxPropertiesMapFilterAction'
        ));

        add_action('wp_ajax_property_enquire', array(
            $this->getPropertyController(),
            'ajaxPropertyEnquireAction'
        ));
        add_action('wp_ajax_nopriv_property_enquire', array(
            $this->getPropertyController(),
            'ajaxPropertyEnquireAction'
        ));

        add_action('init', array($this, 'addEditEndPointSupport'));

        add_filter('user_has_cap', array($this, 'userHasCapFilter'), 10, 4);
    }

    public function userHasCapFilter($allcaps, $caps, $args, $user)
    {
        if (false === ( $allcaps = get_transient("user_{$user->ID}_has_cap") )) {
            global $wpdb;
            $user_group_table = _groups_get_tablename("user_group");
            $rows = $wpdb->get_results($wpdb->prepare(
                            "SELECT group_id "
                            . "FROM $user_group_table "
                            . "WHERE user_id = %d", \Groups_Utility::id($user->ID)
            ));
            if ($rows) {
                $result = array();
                foreach ($rows as $row) {
                    $r = array();
                    $group = \Groups_Group::read($row->group_id);
                    $r['group'] = $group;

                    $group_capability_table = _groups_get_tablename("group_capability");
                    $rows = $wpdb->get_results($wpdb->prepare(
                                    "SELECT capability_id "
                                    . "FROM $group_capability_table "
                                    . "WHERE group_id = %d", \Groups_Utility::id($row->group_id)
                    ));
                    if ($rows) {
                        foreach ($rows as $row) {
                            $cap = \Groups_Capability::read($row->capability_id);
                            $r['cap'][] = $cap;
                            $allcaps[$cap->capability] = true;
                        }
                    }

                    $result[] = $r;
                }
            }

            set_transient("user_{$user->ID}_has_cap", $allcaps, 10);
        }

        return $allcaps;
    }

    public function addEditEndPointSupport()
    {
        add_rewrite_endpoint('edit', EP_PERMALINK | EP_PAGES);
    }

    public function processForms()
    {
        if (isset($_POST['moz-action']) && $_POST['moz-action'] == 'mozart-user-property-submit') {
            $controller = \Mozart::service('realestate.property.controller');
            $controller->processSubmitPropertyFormAction();
            // clear the POST info, because if the post is not published
            // and you submit the edit form, it will return a 404 page
            $_POST = array();
        }
    }

    public function onSavePost($post_id)
    {
        // If this isn't a property post, don't update it.
        if (self::$post_type != $_POST['post_type']) {
            return;
        }

        do_action('onSaveProperty', $post_id);
    }

    public function addShortcodes()
    {
        // @todo: move this to DI Container. Like a Twig Extension
        add_shortcode('property-submit-form', array('\Mozart\RealEstate\PropertyBundle\Shortcode\SubmitPropertyForm',
            'shortcode'));
        add_shortcode('user-submitted-properties-list', array('\Mozart\RealEstate\PropertyBundle\Shortcode\SubmittedPropertiesList',
            'shortcode'));
    }

    public function registerWidgets()
    {
        register_widget('\Mozart\RealEstate\PropertyBundle\Widget\MapProperties');
        register_widget('\Mozart\RealEstate\PropertyBundle\Widget\FeaturedProperties');
        register_widget('\Mozart\RealEstate\PropertyBundle\Widget\FeaturedPropertiesLarge');
        register_widget('\Mozart\RealEstate\PropertyBundle\Widget\MostRecentProperties');
        register_widget('\Mozart\RealEstate\PropertyBundle\Widget\ReducedProperties');
        register_widget('\Mozart\RealEstate\PropertyBundle\Widget\CarouselProperties');
        register_widget('\Mozart\RealEstate\PropertyBundle\Widget\EnquireProperties');
        register_widget('\Mozart\RealEstate\PropertyBundle\Widget\PropertyFilter');
    }

    /**
     * Register google map script for obtaining GPS locations from address
     */
    public function loadAssets()
    {
        if (is_admin()) {
            wp_enqueue_script('googlemaps3');
            wp_enqueue_script('geolocation', \Mozart::parameter('wp.content.uri') . '/mozart' . '/public/bundles/property/js/geolocation.js');
        } else {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-widget');
            wp_enqueue_script('jquery-ui-mouse');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('geolocation', \Mozart::parameter('wp.content.uri') . '/mozart/public/bundles/property/js/geolocation.js');

            wp_enqueue_script(
                    'property-map', \Mozart::parameter('wp.content.uri') . '/mozart' . '/public/bundles/property/js/map.js', array(
                'googlemaps3')
            );
        }
    }

    /**
     * Custom post type
     */
    public function registerPostTypes()
    {
        $labels = array(
            'name' => __('Properties', self::$domain),
            'singular_name' => __('Property', self::$domain),
            'add_new' => __('Add New', self::$domain),
            'add_new_item' => __('Add New Property', self::$domain),
            'edit_item' => __('Edit Property', self::$domain),
            'new_item' => __('New Property', self::$domain),
            'all_items' => __('All Properties', self::$domain),
            'view_item' => __('View Property', self::$domain),
            'search_items' => __('Search Property', self::$domain),
            'not_found' => __('No properties found', self::$domain),
            'not_found_in_trash' => __('No properties found in Trash', self::$domain),
            'parent_item_colon' => '',
            'menu_name' => __('Properties', self::$domain),
        );

        register_post_type(self::$post_type, array(
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail', 'comments', 'author'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => __('properties', self::$domain)),
            'menu_position' => 32,
            'categories' => array('property_types'),
            'menu_icon' => \Mozart::parameter('wp.content.uri') . '/mozart' . '/public/bundles/property/img/properties.png',
                )
        );

        $this->register_metabox();
    }

    private function register_metabox()
    {
        /**
         * Meta options for custom post type
         */
        $property_metabox = new \wpalchemy_MetaBox(array(
            'id' => '_property_meta',
            'title' => __('Property Options', self::$domain),
            'template' => __DIR__ . '/meta.php',
            'types' => array(self::$post_type),
            'prefix' => '_property_',
            'mode' => WPALCHEMY_MODE_EXTRACT,
                )
        );
    }

    /**
     * Custom taxonomies
     */
    public function registerTaxonomies()
    {
        $property_contracts_labels = array(
            'name' => __('Contract Types', self::$domain),
            'singular_name' => __('Contract Type', self::$domain),
            'search_items' => __('Search Contract Types', self::$domain),
            'all_items' => __('All Contract Types', self::$domain),
            'parent_item' => __('Parent Contract Type', self::$domain),
            'parent_item_colon' => __('Parent Contract Type:', self::$domain),
            'edit_item' => __('Edit Contract Type', self::$domain),
            'update_item' => __('Update Contract Type', self::$domain),
            'add_new_item' => __('Add New Contract Type', self::$domain),
            'new_item_name' => __('New Contract Type', self::$domain),
            'menu_name' => __('Contract Type', self::$domain),
        );

        register_taxonomy('property_contracts', self::$post_type, array(
            'labels' => $property_contracts_labels,
            'hierarchical' => true,
            'query_var' => 'property_contract',
            'rewrite' => array('slug' => __('property-contract', self::$domain)),
            'public' => true,
            'show_ui' => true,
        ));

        $property_types_labels = array(
            'name' => __('Property Types', self::$domain),
            'singular_name' => __('Property Type', self::$domain),
            'search_items' => __('Search Property Types', self::$domain),
            'all_items' => __('All Property Types', self::$domain),
            'parent_item' => __('Parent Property Type', self::$domain),
            'parent_item_colon' => __('Parent Property Type:', self::$domain),
            'edit_item' => __('Edit Property Type', self::$domain),
            'update_item' => __('Update Property Type', self::$domain),
            'add_new_item' => __('Add New Property Type', self::$domain),
            'new_item_name' => __('New Property Type', self::$domain),
            'menu_name' => __('Property Type', self::$domain),
        );

        register_taxonomy('property_types', self::$post_type, array(
            'labels' => $property_types_labels,
            'hierarchical' => true,
            'query_var' => 'property_type',
            'rewrite' => array('slug' => __('property-type', self::$domain)),
            'public' => true,
            'show_ui' => true,
        ));

        $property_locations_labels = array(
            'name' => __('Locations', self::$domain),
            'singular_name' => __('Location', self::$domain),
            'search_items' => __('Search Location', self::$domain),
            'all_items' => __('All Locations', self::$domain),
            'parent_item' => __('Parent Location', self::$domain),
            'parent_item_colon' => __('Parent Location:', self::$domain),
            'edit_item' => __('Edit Location', self::$domain),
            'update_item' => __('Update Location', self::$domain),
            'add_new_item' => __('Add New Location', self::$domain),
            'new_item_name' => __('New Location', self::$domain),
            'menu_name' => __('Location', self::$domain),
        );
        register_taxonomy('locations', self::$post_type, array(
            'labels' => $property_locations_labels,
            'hierarchical' => true,
            'query_var' => 'location',
            'rewrite' => array('slug' => __('location', self::$domain)),
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
        ));

        $amenities_labels = array(
            'name' => __('Amenities', self::$domain),
            'singular_name' => __('Amenity', self::$domain),
            'search_items' => __('Search Amenity', self::$domain),
            'all_items' => __('All Amenities', self::$domain),
            'parent_item' => __('Parent Amenity', self::$domain),
            'parent_item_colon' => __('Parent Amenity:', self::$domain),
            'edit_item' => __('Edit Amenity', self::$domain),
            'update_item' => __('Update Amenity', self::$domain),
            'add_new_item' => __('Add New Amenity', self::$domain),
            'new_item_name' => __('New Amenity', self::$domain),
            'menu_name' => __('Amenity', self::$domain),
        );

        register_taxonomy('amenities', self::$post_type, array(
            'labels' => $amenities_labels,
            'hierarchical' => true,
            'query_var' => 'amenity',
            'rewrite' => array('slug' => __('amenity', self::$domain)),
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
        ));
    }

    /**
     * Custom columns
     */
    public function custom_post_columns()
    {
        return array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title', self::$domain),
            'thumbnail' => __('Thumbnail', self::$domain),
            'optional_title' => __('Optional Title', self::$domain),
            'price' => __('Price', self::$domain),
            'location' => __('Location', self::$domain),
            'property_types' => __('Property Type', self::$domain),
            'gps' => __('GPS', self::$domain),
            'contract_type' => __('Contract Type', self::$domain),
            'featured' => __('Featured', self::$domain),
            'reduced' => __('Reduced', self::$domain),
            'agents' => __('Agents', self::$domain),
            'author' => __('Author', self::$domain),
        );
    }

    public function custom_post_manage($column, $post_id)
    {
        global $post;

        switch ($column) {
            case 'thumbnail':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, 'admin-thumb');
                } else {
                    echo '<img src="' . get_template_directory_uri() . '/Resources/public/img/property-tmp-small.png' . '" width="80">';
                }
                break;
            case 'optional_title':
                $title = get_post_meta($post_id, '_property_title', true);
                if (!empty($title)) {
                    echo $title;
                } else {
                    echo '<span style="color: red">' . __('Undefined', self::$domain) . '</span>';
                }
                break;
            case 'price':
                $price = get_post_meta($post_id, '_property_price', true);
                if (empty($price)) {
                    echo '<span style="color: red">' . __('Undefined', self::$domain) . '</span>';
                } else {
                    echo $price;
                }
                break;
            case 'location':
                if (!is_array(get_the_terms($post, 'locations'))) {
                    echo '<span style="color: red">' . __('Undefined', self::$domain) . '</span>';
                } else {
                    $terms = (array) get_the_terms($post, 'locations');
                    $location = array_shift($terms);
                    echo '<a href="?post_type=property&location=' . $location->slug . '">' . $location->name . '</a>';
                }
                break;
            case 'property_types':
                if (!is_array(get_the_terms($post, 'locations'))) {
                    echo '<span style="color: red">' . __('Undefined', self::$domain) . '</span>';
                } else {
                    $terms = (array) get_the_terms($post, 'property_types');
                    $property_type = array_shift($terms);
                    echo '<a href="?post_type=property&property_type=' . $property_type->slug . '">' . $property_type->name . '</a>';
                }
                break;
            case 'featured':
                $featured = get_post_meta($post_id, '_property_featured');
                if ($featured) {
                    echo '<span style="color:green;">' . __('On', self::$domain) . '</span>';
                } else {
                    echo '<span style="color:red;">' . __('Off', self::$domain) . '</span>';
                }
                break;
            case 'gps':
                $longitude = get_post_meta($post_id, '_property_longitude', true);
                $latitude = get_post_meta($post_id, '_property_latitude', true);
                if (!$longitude || !$latitude) {
                    echo '<span style="color: red">' . __('Missing', self::$domain) . '</span>';
                } else {
                    echo '[' . $latitude . ', ' . $longitude . ']';
                }
                break;
            case 'reduced':
                $reduced = get_post_meta($post_id, '_property_reduced');
                if ($reduced) {
                    echo '<span style="color:green;">' . __('On', self::$domain) . '</span>';
                } else {
                    echo '<span style="color:red;">' . __('Off', self::$domain) . '</span>';
                }
                break;
            case 'contract_type':
                if (!is_array(get_the_terms($post, 'property_contracts'))) {
                    echo '<span style="color: red">' . __('Undefined', self::$domain) . '</span>';
                } else {
                    $terms = get_the_terms($post, 'property_contracts');
                    $contract_type = array_shift($terms);
                    echo '<a href="?post_type=property&property_contract=' . $contract_type->slug . '">' . $contract_type->name . '</a>';
                }
                break;
            case 'agents':
                $agents = get_post_meta($post_id, '_property_agents', true);
                if (!is_array($agents)) {
                    echo '<span style="color:red;">' . __('Not assigned', self::$domain) . '</span>';
                } else {
                    foreach ($agents as $agent_id) {
                        echo get_post($agent_id)->post_title . '<br>';
                    }
                }
                break;
        }
    }

    /**
     * Change posts per page
     */
    public function modify_posts_per_properties_page()
    {
        add_filter('option_posts_per_page', array($this, 'option_posts_per_properties_page'));
    }

    public function option_posts_per_properties_page($value)
    {
        if (is_post_type_archive(self::$post_type) || is_tax('locations') || is_tax('amenities') || is_tax('property_types')) {
            return parameter(self::$post_type, 'properties', 'per_page');
        }

        return $value;
    }

    private function getPropertyController()
    {
        return $this->container->get('realestate.property.controller');
    }

}
