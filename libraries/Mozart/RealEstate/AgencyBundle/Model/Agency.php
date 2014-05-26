<?php

namespace Mozart\RealEstate\AgencyBundle\Model;

class Agency
{
    public function get($count = -1)
    {
        $query = new \WP_Query(array(
            'post_type' => 'agency',
            'posts_per_page' => $count,
        ));

        return $query->posts;
    }

    public function get_assigned($property, $count = 3)
    {
        $agencies = get_post_meta($property->ID, '_property_agencies', true);

        if (!is_array($agencies)) {
            return array();
        }

        $query = new \WP_Query(array(
            'post__in' => array_values($agencies),
            'post_type' => 'agency',
            'posts_per_page' => $count,
        ));

        return $this->prepare($query);
    }

    public function get_properties_count($agency_id)
    {
        return count(\Mozart::service('realestate.property.model')->select('_property_agencies', $agency_id));
    }

    public function update_properties_counter($agency_id)
    {
        // cache as a meta value the properties counter
        update_post_meta(
                $agency_id, '_agency_properties_counter', $this->get_properties_count($agency_id)
        );
    }

    public function prepare($query)
    {
        $results = array();

        foreach ($query->posts as $agency) {
            $agency->meta = get_post_meta($agency->ID, '', true);
            $results[] = $agency;
        }

        return $results;
    }

}
