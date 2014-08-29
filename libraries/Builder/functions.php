<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

if ( ! function_exists( 'vc_manager' ) ) {
	/**
	 * Visual Composer manager.
	 *
	 * @return Mozart\Bundle\BuilderBundle\Manager\VisualComposerManager
	 */
	function vc_manager() {
		return Mozart::service('builder.manager');
	}
}