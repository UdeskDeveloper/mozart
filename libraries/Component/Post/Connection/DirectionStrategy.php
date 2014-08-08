<?php
namespace Mozart\Component\Post\Connection;

interface DirectionStrategy
{
    public function get_arrow();
    public function choose_direction( $direction );
    public function directions_for_admin( $direction, $show_ui );
    public function get_directed_class();
}
