<?php
namespace MOC\MocMessageQueue\Message;

/**
 * Simple String based message for the message queue systems
 *
 * This message holds a simple string payload. It is primarily used for testing purposes.
 *
 * @package MOC\MocMessageQueue
 */
class StringMessage extends AbstractMessage implements MessageInterface {

	/**
	 * @var string
	 */
	protected $payload;

	/**
	 * @param string $payload
	 */
	public function __construct($payload) {
		$this->payload = $payload;
	}

	/**
	 * @return string
	 */
	public function getPayload() {
		return $this->payload;
	}

}