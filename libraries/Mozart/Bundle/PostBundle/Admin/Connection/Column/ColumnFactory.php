<?php

namespace Mozart\Bundle\PostBundle\Admin\Connection\Column;

use Mozart\Bundle\PostBundle\Admin\Connection\Factory;

class ColumnFactory extends Factory {

	protected $key = 'admin_column';

	function __construct() {
		parent::__construct();

		add_action( 'load-edit.php', array( $this, 'add_items' ) );
		add_action( 'load-users.php', array( $this, 'add_items' ) );
	}

	function add_item( $directed, $object_type, $post_type, $title ) {
		$class = 'P2P_Column_' . ucfirst( $object_type );
		$column = new $class( $directed );

		$screen = get_current_screen();

		add_filter( "manage_{$screen->id}_columns", array( $column, 'add_column' ) );
		add_action( 'admin_print_styles', array( $column, 'styles' ) );
	}
}
