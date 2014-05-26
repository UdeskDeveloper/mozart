<?php

namespace Mozart\RealEstate\PropertyBundle\Model;

class Property
{

    protected $statuses = array();

    public function __construct()
    {
        $this->setDefaultStatuses();
    }

    private function setDefaultStatuses()
    {
        $this->statuses = array(
            'any' => __('All Properties'),
            'publish' => __('Published'),
            'pending' => __('Pending Review'),
            'draft' => __('Draft'),
            'trash' => __('Trashed')
        );
    }

    public function addStatus($newStatus)
    {
        if (!empty($newStatus)) {
            $this->statuses[] = $newStatus;
        }
    }

    public function getStatuses()
    {
        return $this->statuses;
    }

    /**
     * Get featured properties
     *
     * @param int
     *
     * @return array()
     */
    public function get_featured($count = 3, $shuffle = FALSE)
    {
        $args = array(
            'post_type' => 'property',
            'posts_per_page' => $count,
            'meta_query' => array(
                array(
                    'key' => '_property_featured',
                    'value' => '1',
                    'compare' => '=',
                ),
            ),
        );

        if ($shuffle) {
            $args['orderby'] = 'rand';
        }

        $query = new \WP_Query($args);

        return $this->prepare($query);
    }

    /**
     * Get most recent properties
     *
     * @return array()
     */
    public function getMostRecent($count = -1, $shuffle = false)
    {
        $args = array(
            'post_type' => 'property',
            'posts_per_page' => $count,
        );

        if ($shuffle) {
            $args['orderby'] = 'rand';
        }

        $query = new \WP_Query($args);

        return $this->prepare($query);
    }

    public function prepareForMapDisplay($properties)
    {
        $filtered_properties = array();
        $properties_gps = array();

        foreach ($properties as $property) {
            if (!empty($property->_property_latitude)
                    && !empty($property->_property_longitude)) {
                $lat = $property->_property_latitude;
                $long = $property->_property_longitude;
                if (is_numeric($lat)
                        && is_numeric($long)
                        && !in_array($lat . $long, $properties_gps)) {
                    $properties_gps[] = $lat . $long;
                    $filtered_properties[] = $property;
                }
            }
        }

        unset($properties_gps);

        return $filtered_properties;
    }

    /**
     * Get reduced properties
     *
     * @return array()
     */
    public function get_reduced($count = 3, $shuffle = FALSE)
    {
        $args = array(
            'post_type' => 'property',
            'posts_per_page' => $count,
            'meta_query' => array(
                array(
                    'key' => '_property_reduced',
                    'value' => '1',
                    'compare' => '='
                )
            )
        );

        if ($shuffle) {
            $args['orderby'] = 'rand';
        }

        $query = new \WP_Query($args);

        return $this->prepare($query);
    }

    public function select($meta_key, $post_id, $count = -1)
    {
        $query = new \WP_Query(array(
            'post_type' => 'property',
            'posts_per_page' => $count,
            'meta_query' => array(
                array(
                    'key' => $meta_key,
                    'value' => '"' + $post_id + '"',
                    'compare' => 'LIKE',
                )
            )
        ));

        return $this->prepare($query);
    }

    /**
     * Prepare meta information for properties
     *
     * @return array()
     */
    public function prepare(\WP_Query $query)
    {
        $results = array();

        foreach ($query->posts as $property) {
            $property->meta = get_post_meta($property->ID, '', true);
            $property->location = wp_get_post_terms($property->ID, 'locations');
            $property->property_types = wp_get_post_terms($property->ID, 'property_types');
            $property->property_contracts = wp_get_post_terms($property->ID, 'property_contracts');
            $property->slides = get_post_meta($property->ID, '_property_slides', true);
            $property->slider_image = get_post_meta($property->ID, '_property_slider_image', true);
            $results[] = $property;
        }

        return $results;
    }

    public function prepare_single($post)
    {
        $post->meta = get_post_meta($post->ID, '', true);
        $post->location = wp_get_post_terms($post->ID, 'locations');
        $post->property_types = wp_get_post_terms($post->ID, 'property_types');
        $post->property_contracts = wp_get_post_terms($post->ID, 'property_contracts');
        $post->slides = get_post_meta($post->ID, '_property_slides', true);
        $post->slider_image = get_post_meta($post->ID, '_property_slider_image', true);

        return $post;
    }

    public function getProperties(array $options = array(), $return_query = false)
    {
        $queryArgs = array(
            'post_type' => 'property',
            'posts_per_page' => -1,
            'tax_query' => array(),
            'meta_query' => array(),
            'order_by' => 'title',
            'order' => 'DESC',
            'paged' => ( get_query_var('paged') ) ? get_query_var('paged') : 1
        );

        if (!empty($options['filter_order'])) {
            $queryArgs['order'] = $options['filter_order'];
        }

        if (!empty($options['filter_sort_by'])) {
            if ($options['filter_sort_by'] == 'price') {
                $queryArgs['orderby'] = 'meta_value_num';
                $queryArgs['meta_key'] = '_property_price';
            }

            if ($options['filter_sort_by'] == 'beds') {
                $queryArgs['orderby'] = 'meta_value_num';
                $queryArgs['meta_key'] = '_property_bedrooms';
            }

            if ($options['filter_sort_by'] == 'baths') {
                $queryArgs['orderby'] = 'meta_value_num';
                $queryArgs['meta_key'] = '_property_bathrooms';
            }

            if ($options['filter_sort_by'] == 'date') {
                $queryArgs['orderby'] = 'date';
            }

            if ($options['filter_sort_by'] == 'title') {
                $queryArgs['orderby'] = 'title';
            }
        }

        if (!empty($options['filter_location'])) {
            $queryArgs['tax_query'][] = array(
                'taxonomy' => 'locations',
                'field' => 'id',
                'terms' => $options['filter_location'],
                'operator' => 'IN',
            );
        }

        if (!empty($options['filter_type'])) {
            if (is_array($options['filter_type']) && count($options['filter_type']) > 0) {
                $terms = array();

                foreach ($options['filter_type'] as $type) {
                    $terms[] = $type;
                }
                $queryArgs['tax_query'][] = array(
                    'taxonomy' => 'property_types',
                    'field' => 'id',
                    'terms' => $terms,
                    'operator' => 'IN',
                );
            } else {
                $queryArgs['tax_query'][] = array(
                    'taxonomy' => 'property_types',
                    'field' => 'id',
                    'terms' => $options['filter_type'],
                    'operator' => 'IN',
                );
            }
        }

        if (!empty($options['filter_bedrooms'])) {
            $queryArgs['meta_query'][] = array(
                'key' => '_property_bedrooms',
                'value' => $options['filter_bedrooms'],
                'compare' => '>=',
                'type' => 'numeric',
            );
        }

        if (!empty($options['filter_bathrooms'])) {
            $queryArgs['meta_query'][] = array(
                'key' => '_property_bathrooms',
                'value' => $options['filter_bathrooms'],
                'compare' => '>=',
                'type' => 'numeric',
            );
        }

        if (!empty($options['filter_contract_type'])) {
            $queryArgs['tax_query'][] = array(
                'taxonomy' => 'property_contracts',
                'field' => 'id',
                'terms' => $options['filter_contract_type'],
                'operator' => 'IN',
            );
        }

        // Area
        if (!empty($options['filter_area_from']) && !empty($options['filter_area_to'])) {
            $queryArgs['meta_query'][] = array(
                'key' => '_property_area',
                'value' => array($options['filter_area_from'], $options['filter_area_to']),
                'type' => 'numeric',
                'compare' => 'BETWEEN'
            );
        } elseif (!empty($options['filter_area_from'])) {
            $queryArgs['meta_query'][] = array(
                'key' => '_property_area',
                'value' => $options['filter_area_from'],
                'type' => 'numeric',
                'compare' => '>='
            );
        } elseif (!empty($options['filter_area_to'])) {
            $queryArgs['meta_query'][] = array(
                'key' => '_property_area',
                'value' => $options['filter_area_to'],
                'type' => 'numeric',
                'compare' => '<='
            );
        }

        // Price
        if (!empty($options['filter_price_range'])) {
            $priceRange = explode(',', $options['filter_price_range']);
            $queryArgs['meta_query'][] = array(
                'key' => '_property_price',
                'value' => $priceRange,
                'type' => 'numeric',
                'compare' => 'BETWEEN'
            );
        }

        $wp_query = new \WP_Query();

        $wp_query->query($queryArgs);

        if ($return_query) {
            return $wp_query;
        }

        return $this->prepare($wp_query);
    }

    /**
     * Users submissions
     *
     * @param array $args
     *
     * @return array|bool
     */
    public function getUserSubmittedProperties(array $args = array(), $override_loop = false)
    {
        $defaults = array(
            'post_type' => 'property',
            'post_status' => 'any',
            'author' => get_current_user_id(),
            'paged' => get_query_var('paged'),
        );

        $r = wp_parse_args($args, $defaults);

        if ($override_loop) {
            return query_posts($r);
        }
        return get_posts($r);
    }

    /**
     * Count by status the user submitted properties
     *
     * @param array $args
     *
     * @return array
     */
    public function getUserSubmittedPropertiesCounters(array $args = array())
    {
        $args['posts_per_page'] = -1;
        $properties = $this->getUserSubmittedProperties($args);

        $counters = array();

        foreach ($this->statuses as $keyStatus => $status) {
            $counters[$keyStatus] = 0;
        }

        $counters['any'] = count($properties);

        foreach ($properties as $property) {
            $counters[$property->post_status] ++;
        }

        return $counters;
    }

    public function submission_is_paid($user_id, $post_id)
    {
        $query = array(
            'post_type' => 'transaction',
            'meta_query' => array(
                array(
                    'key' => '_transaction_user_id',
                    'value' => $user_id,
                ),
                array(
                    'key' => '_transaction_post_id',
                    'value' => $post_id,
                )
            )
        );

        $wp_query = new WP_Query($query);

        return $wp_query->have_posts();
    }

    /**
     * Helper function to create submission url link
     * @param $params
     * @return string $link
     */
    public function submission_create_link($params)
    {
        $page = $this->get_submission_page();
        $permalink = get_permalink($page) . '?' . http_build_query($params);

        return $permalink;
    }

    /**
     * Helper function to get submission page index
     * @return bool|mixed
     */
    public function get_submission_page()
    {
        static $submissionPage;

        if ($submissionPage) {
            return $submissionPage;
        } else {
            $pages = get_posts(array(
                'post_type' => 'page',
                'meta_key' => '_wp_page_template',
                'meta_value' => 'page-submission-index.php',
            ));
        }

        if (count($pages)) {
            $submissionPage = reset($pages);
        } else {
            $submissionPage = FALSE;
        }

        return $submissionPage;
    }

}
