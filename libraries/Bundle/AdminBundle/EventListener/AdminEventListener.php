<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\AdminBundle\EventListener;


use Mozart\Component\Admin\AdminEvents;

class AdminEventListener {

	public function __construct()
	{
	}

	public function onApplicationInit(){
		add_filter(
			AdminEvents::HEAD,
			function () {
				\Mozart::dispatch( AdminEvents::HEAD );
			},
			0
		);
		add_filter(
			AdminEvents::MENU,
			function () {
				\Mozart::dispatch( AdminEvents::MENU );
			},
			0
		);
	}
} 