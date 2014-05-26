<?php

namespace Mozart\RealEstate\LandlordBundle\Model;

class Landlord
{
    public function get_list()
    {
        $query = new \WP_Query(array(
            'post_type' => 'landlord',
            'posts_per_page' => - 1,
                ));

        return $query->posts;
    }

    public function get_property_list_by_id($id)
    {
        $query = new \WP_Query(array(
            'post_type' => 'property',
            'posts_per_page' => - 1,
            'meta_query' => array(
                array(
                    'key' => '_property_landlord',
                    'value' => $id,
                    'type' => 'numeric'
                ),
            ),
                ));

        return $query->posts;
    }
}
