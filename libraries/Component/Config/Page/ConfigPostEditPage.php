<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config\Page;

use Mozart\Component\Post\Type\PostTypeInterface;

class ConfigPostEditPage extends ConfigPage
{
	/**
	 * @var \Mozart\Component\Post\Type\PostTypeInterface
	 */
	private $postType;

	public function __construct(PostTypeInterface $postType)
	{
		$this->postType = $postType;
	}

	public function getType()
	{
		return 'post_type';
	}

	public function getKey()
	{
		return $this->postType->getKey();
	}

} 