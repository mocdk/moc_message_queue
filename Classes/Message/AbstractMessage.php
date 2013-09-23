<?php
namespace MOC\MocMessageQueue\Message;

/**
 * Abstract message class
 *
 * Extend this class to implement you own messages
 *
 * @package MOC\MocMessageQueue
 */
abstract class AbstractMessage {

	/**
	 * @var string
	 */
	protected $identifier;

	/**
	 * Return a unique identifier for this message
	 *
	 * @return string
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Set the unique identifier for this message.
	 *
	 * @param string $identifier
	 * @return void
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}

}
