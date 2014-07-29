<?php

namespace Mozart\Bundle\PostBundle\Admin\Connection\Dropdown;

use Mozart\Bundle\PostBundle\Admin\Connection\Factory;

class DropdownFactory extends Factory {

	protected $key = 'admin_dropdown';

	function __construct() {
		parent::__construct();

		add_action( 'load-edit.php', array( $this, 'add_items' ) );
		add_action( 'load-users.php', array( $this, 'add_items' ) );
	}

	function add_item( $directed, $object_type, $post_type, $title ) {
		$class = 'P2P_Dropdown_' . ucfirst( $object_type );
		$item = new $class( $directed, $title );
	}
}

