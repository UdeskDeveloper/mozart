<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Event;

use Symfony\Component\EventDispatcher\Event;

class WordpressEvent extends Event
{
	/**
	 * @var array
	 */
	protected $parameters;

	/**
	 * Constructor
	 *
	 * @param array $parameters
	 */
	public function __construct(array $parameters = array())
	{
		$this->parameters = $parameters;
	}

	/**
	 * Returns if parameter gor given index position exists
	 *
	 * @param mixed $index
	 *
	 * @return bool
	 */
	public function hasParameter($index)
	{
		return isset( $this->parameters[$index] );
	}

	/**
	 * Returns a parameter of given index position
	 *
	 * @param mixed $index
	 *
	 * @return mixed
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getParameter($index)
	{
		if (!$this->hasParameter( $index )) {
			throw new \InvalidArgumentException( sprintf( 'Cannot retrieve parameter "%s"', $index ) );
		}

		return $this->parameters[$index];
	}

	/**
	 * Adds a parameter
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function addParameter($value)
	{
		$this->parameters[] = $value;

		return $this;
	}
} 