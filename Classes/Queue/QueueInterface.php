<?php
namespace MOC\MocMessageQueue\Queue;

use MOC\MocMessageQueue\Message\MessageInterface;

/**
 * Interface for a message queue
 *
 * @package MOC\MocMessageQueue
 */
interface QueueInterface {

	/**
	 * Publish a message in the message queue
	 *
	 * @param MessageInterface $message
	 * @return boolean Return TRUE if the message was successfully published
	 */
	public function publish(MessageInterface $message);

	/**
	 * Wait until a message is available and reserve that message for processing
	 *
	 * When the message is properly handled, the finish method
	 *
	 * @param integer $timeout The timeout in seconds. NULL means forever
	 * @return \MOC\MocMessageQueue\Message\MessageInterface
	 */
	public function waitAndReserve($timeout = NULL);

	/**
	 * Mark a message as done
	 *
	 * This must be called for every message that was reserved and that was
	 * processed successfully.
	 *
	 * @param MessageInterface $message
	 * @return boolean TRUE if the message could be removed
	 */
	public function finish(MessageInterface $message);

	/**
	 * Count messages in the queue
	 *
	 * @return integer
	 */
	public function count();

}