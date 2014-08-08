<?php
namespace Mozart\Component\Post\Connection\ConnectionType;

class ReciprocalConnectionType extends IndeterminateConnectionType
{
    public function choose_direction($direction)
    {
        return 'any';
    }

    public function directions_for_admin($direction, $show_ui)
    {
        if ($show_ui) {
            $directions = array( 'any' );
        } else {
            $directions = array();
        }

        return $directions;
    }
}
