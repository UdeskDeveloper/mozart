<?php

namespace Mozart\RealEstate\AgentBundle\Model;

class Agent
{
    public function get($count = 3)
    {
        $query = new \WP_Query(array(
            'post_type' => 'agent',
            'posts_per_page' => $count,
                ));

        return $this->prepare($query);
    }

    public function get_assigned($property, $count = 3)
    {
        $agents = get_post_meta($property->ID, '_property_agents', true);
        if (!is_array($agents)) {
            return array();
        }

        $query = new \WP_Query(array(
            'post__in' => array_values($agents),
            'post_type' => 'agent',
            'posts_per_page' => $count,
                ));

        return $this->prepare($query);
    }

    public function get_properties_count($agent_id)
    {
        return count(\Mozart::service('realestate.property.model')
                        ->select('_property_agents', $agent_id));
    }

    public function update_properties_counter($agent_id)
    {
        // cache as a meta value the properties counter
        update_post_meta(
                $agent_id, '_agent_properties_counter', $this->get_properties_count($agent_id)
        );
    }

    public function prepare(\WP_Query $query)
    {
        $results = array();

        foreach ($query->posts as $agent) {
            $agent->meta = get_post_meta($agent->ID, '', true);
            $results[] = $agent;
        }

        return $results;
    }

    public function filter($return_query = FALSE, $use_pager = true)
    {
        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
        $wp_query = new \WP_Query();

        $agents = array(
            'post_type' => 'agent',
            'posts_per_page' => -1,
            'tax_query' => array(),
            'meta_query' => array(),
            'order_by' => 'published'
        );

        if ($use_pager) {
            $agents['posts_per_page'] = parameter('agent', 'agents', 'per_page');
            $agents['paged'] = $paged;
        }

        if (!empty($_GET['filter_order'])) {
            $agents['order'] = $_GET['filter_order'];
        } else {
            $agents['order'] = 'DESC';
        }

        $agents['orderby'] = 'date';
        if (!empty($_GET['filter_sort_by'])) {
            if ($_GET['filter_sort_by'] == 'name') {
                $agents['orderby'] = 'title';
            } elseif ($_GET['filter_sort_by'] == 'rating') {
                $agents['orderby'] = 'meta_value_num';
                $agents['meta_key'] = '_kksr_ratings';
            } elseif ($_GET['filter_sort_by'] == 'listings_count') {
                $agents['orderby'] = 'meta_value_num';
                $agents['meta_key'] = '_agent_properties_counter';
            } elseif ($_GET['filter_sort_by'] == 'date') {
                $agents['orderby'] = 'date';
            }
        }

        if (!empty($_GET['filter_location'])) {
            $agents['tax_query'][] = array(
                'taxonomy' => 'locations',
                'field' => 'id',
                'terms' => $_GET['filter_location'],
                'operator' => 'IN',
            );
        }

        if (!empty($_GET['filter_type'])) {
            if (is_array($_GET['filter_type']) && count($_GET['filter_type']) > 0) {
                $terms = array();

                foreach ($_GET['filter_type'] as $type) {
                    $terms[] = $type;
                }
                $agents['tax_query'][] = array(
                    'taxonomy' => 'property_types',
                    'field' => 'id',
                    'terms' => $terms,
                    'operator' => 'IN',
                );
            } else {
                $agents['tax_query'][] = array(
                    'taxonomy' => 'property_types',
                    'field' => 'id',
                    'terms' => $_GET['filter_type'],
                    'operator' => 'IN',
                );
            }
        }

        if (!empty($_GET['filter_bedrooms'])) {
            $agents['meta_query'][] = array(
                'key' => '_property_bedrooms',
                'value' => $_GET['filter_bedrooms'],
                'compare' => '>=',
                'type' => 'numeric',
            );
        }

        if (!empty($_GET['filter_bathrooms'])) {
            $agents['meta_query'][] = array(
                'key' => '_property_bathrooms',
                'value' => $_GET['filter_bathrooms'],
                'compare' => '>=',
                'type' => 'numeric',
            );
        }

        if (!empty($_GET['filter_contract_type'])) {
            $agents['tax_query'][] = array(
                'taxonomy' => 'property_contracts',
                'field' => 'id',
                'terms' => $_GET['filter_contract_type'],
                'operator' => 'IN',
            );
        }

        // Area
        if (!empty($_GET['filter_area_from']) && !empty($_GET['filter_area_to'])) {
            $agents['meta_query'][] = array(
                'key' => '_property_area',
                'value' => array($_GET['filter_area_from'], $_GET['filter_area_to']),
                'type' => 'numeric',
                'compare' => 'BETWEEN'
            );
        } elseif (!empty($_GET['filter_area_from'])) {
            $agents['meta_query'][] = array(
                'key' => '_property_area',
                'value' => $_GET['filter_area_from'],
                'type' => 'numeric',
                'compare' => '>='
            );
        } elseif (!empty($_GET['filter_area_to'])) {
            $agents['meta_query'][] = array(
                'key' => '_property_area',
                'value' => $_GET['filter_area_to'],
                'type' => 'numeric',
                'compare' => '<='
            );
        }

        // Price
        if (!empty($_GET['filter_price_from']) && !empty($_GET['filter_price_to'])) {
            $agents['meta_query'][] = array(
                'key' => '_property_price',
                'value' => array($_GET['filter_price_from'], $_GET['filter_price_to']),
                'type' => 'numeric',
                'compare' => 'BETWEEN'
            );
        } elseif (!empty($_GET['filter_price_from'])) {
            $agents['meta_query'][] = array(
                'key' => '_property_price',
                'value' => $_GET['filter_price_from'],
                'type' => 'numeric',
                'compare' => '>='
            );
        } elseif (!empty($_GET['filter_price_to'])) {
            $agents['meta_query'][] = array(
                'key' => '_property_price',
                'value' => $_GET['filter_price_to'],
                'type' => 'numeric',
                'compare' => '<='
            );
        }

        $wp_query->query($agents);

        if ($return_query) {
            return $wp_query;
        }

        return $this->prepare($wp_query);
    }
}
