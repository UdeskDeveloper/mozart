<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PostBundle\EventListener;

use Mozart\Component\Post\Type\PostTypeManager;

class PostEventListener
{
	/**
	 * @var \Mozart\Component\Post\Type\PostTypeManager
	 */
	private $manager;

	public function __construct(PostTypeManager $manager)
	{

		$this->manager = $manager;
	}

	public function onWordpressInit()
	{
		$this->registerPostTypes();
	}

	private function registerPostTypes()
	{
		$postTypes = $this->manager->getPostTypes();

		foreach ($postTypes as $key => $postType) {
			register_post_type( $key, $postType->getConfiguration() );

			if (is_admin() && false !== ( $extensions = $this->manager->getExtensions( $key ) )
			) {
				foreach ($extensions as $extension) {
					$extension->load( $postType );
				}
			}
		}
	}
} 