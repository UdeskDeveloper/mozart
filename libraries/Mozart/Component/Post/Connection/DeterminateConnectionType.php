<?php
namespace Mozart\Component\Post\Connection;

class DeterminateConnectionType implements DirectionStrategy
{
    public function get_arrow()
    {
        return '&rarr;';
    }

    public function choose_direction($direction)
    {
        return $direction;
    }

    public function directions_for_admin($direction, $show_ui)
    {
        return array_intersect(
            _p2p_expand_direction( $show_ui ),
            _p2p_expand_direction( $direction )
        );
    }

    public function get_directed_class()
    {
        return 'P2P_Directed_Connection_Type';
    }
}
