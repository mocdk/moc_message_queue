<?php
namespace MOC\MocMessageQueue\Message;

class StringMessage extends AbstractMessage implements MessageInterface {

	/**
	 * @var string
	 */
	protected $payload;

	/**
	 * @param $payload
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
