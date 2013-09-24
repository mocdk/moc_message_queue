<?php
namespace MOC\MocMessageQueue\Queue;

use MOC\MocMessageQueue\Message\MessageInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Beanstalk implementation of MOC message queue interface
 *
 * This class requires Pheanstalk (which is bundled with the extensions)
 *
 * @package MOC\MocMessageQueue
 */
class BeanstalkQueue implements QueueInterface {

	/**
	 * @var string
	 */
	public static $server = '127.0.0.1';

	/**
	 * @var string
	 */
	public static $tube = 'moc_message_queue';

	/**
	 * @var \Pheanstalk_Pheanstalk
	 */
	protected $pheanstalk;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->pheanstalk = new \Pheanstalk_Pheanstalk(static::$server);
		$this->pheanstalk->useTube(static::$tube);
	}

	/**
	 * Publish a message in the message queue
	 *
	 * @param MessageInterface $message
	 * @return boolean Return TRUE if the message was successfully published
	 */
	public function publish(MessageInterface $message) {
		$message->setIdentifier($this->pheanstalk->put(serialize($message)));
		GeneralUtility::devLog('Publishing message ' . get_class($message) . ' via beanstalk.', 'moc_message_queue');
		return TRUE;
	}

	/**
	 * Wait until a message is available and reserve that message for processing.
	 *
	 * When the message is properly handled, the finish method is called.
	 *
	 * @param integer $timeout The timeout in seconds. NULL means forever
	 * @return \MOC\MocMessageQueue\Message\MessageInterface|NULL
	 * @throws \MOC\MocMessageQueue\Queue\UnknownMessageException
	 */
	public function waitAndReserve($timeout = NULL) {
		if ($timeout === NULL) {
			$timeout = $this->defaultTimeout;
		}
		$pheanstalkJob = $this->pheanstalk->watch(static::$tube)->ignore('default')->reserve($timeout);
		if ($pheanstalkJob === FALSE) {
			return NULL;
		}
		$message = unserialize($pheanstalkJob->getData());

		if (!($message instanceof MessageInterface)) {
			$this->pheanstalk->delete($pheanstalkJob);
			throw new UnknownMessageException('The message queue tried to fetch a message from the queue that did not implement the correct Message interface. Message permanently removed. The de-serialized class is ' . get_class($message));
		}

		$message->setIdentifier($pheanstalkJob->getId());
		return $message;
	}

	/**
	 * Mark a message as done
	 *
	 * This must be called for every message that was reserved and that was
	 * processed successfully.
	 *
	 * @param MessageInterface $message
	 * @return boolean TRUE if the message could be removed
	 */
	public function finish(MessageInterface $message) {
		$this->pheanstalk->delete($this->pheanstalk->peek($message->getIdentifier()));
		return TRUE;
	}

	/**
	 * Count messages in the queue
	 *
	 * @return integer
	 */
	public function count() {
		$clientStats = $this->pheanstalk->statsTube(self::$tube);
		return $clientStats['current-jobs-ready'];
	}

}