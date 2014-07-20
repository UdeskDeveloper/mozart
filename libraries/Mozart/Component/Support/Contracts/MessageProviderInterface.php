<?php namespace Mozart\Component\Support\Contracts;

interface MessageProviderInterface {

	/**
	 * Get the messages for the instance.
	 *
	 * @return \Mozart\Component\Support\MessageBag
	 */
	public function getMessageBag();

}
