<?php

namespace AC\Mutate\Application;
use \Symfony\Component\Console\Output\Output;

/**
 * Dummy output type for storing all messages written.  Messages are logged as an array.
 */
class ArrayOutput extends Output implements \IteratorAggregate, \Countable {
	protected $messages = array();
	
	public function doWrite($message, $newline)	{
		$this->messages[] = $message;
	}
	
	/**
	 * Return array of stored messages.
	 *
	 * @return array
	 */
	public function getMessages() {
		return $this->messages;
	}
	
	public function getIterator() {
		return new \ArrayIterator($this->messages);
	}
	
	public function count() {
		return count($this->messages);
	}
}