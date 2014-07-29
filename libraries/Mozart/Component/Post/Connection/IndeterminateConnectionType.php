<?php
namespace Mozart\Component\Post\Connection;

class IndeterminateConnectionType implements DirectionStrategy {

	function get_arrow() {
		return '&harr;';
	}

	function choose_direction( $direction ) {
		return 'from';
	}

	function directions_for_admin( $_, $show_ui ) {
		return array_intersect(
			_p2p_expand_direction( $show_ui ),
			_p2p_expand_direction( 'any' )
		);
	}

	function get_directed_class() {
		return 'P2P_Indeterminate_Directed_Connection_Type';
	}
}

