<?php
namespace MOC\MocMessageQueue\Queue;

use MOC\MocMessageQueue\Message\MessageInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Beanstalk implementation of MOC message queue queue interface
 *
 * This class requires Pheanstalk (is bundled with the extensions)
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
	public static $tube = 'moc_event_queue';

	/**
	 * @var \Pheanstalk_Pheanstalk
	 */
	protected $pheanstalk;

	public function __construct() {
		$this->pheanstalk = new \Pheanstalk_Pheanstalk(static::$server);
		$this->pheanstalk->useTube(static::$tube);
	}

	/**
	 * Publish a message in the messagequeue
	 *
	 * @param MessageInterface $message
	 * @return boolean Return TRUE if the message was successfully published
	 */
	public function publish(MessageInterface $message) {
		$message->setIdentifier($this->pheanstalk->put(serialize($message)));
		GeneralUtility::devLog('Publishing message ' . get_class($message) . ' via beanstalk.', 'moc_messagequeue');
	}

	/**
	 * Wait until a message is available and reserve that message for processing
	 *
	 * When the message is properly handled, the finish method
	 *
	 * @param integer $timeout The timeout in seconds. NULL means forever
	 * @return MOC\MocMessageQueue\Message\MessageInterface
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
		// @todo validate that the messageinterface is actually implemented!
		$message->setIdentifier($pheanstalkJob->getId());
		return $message;
	}

	/**
	 * Mark a message as done
	 *
	 * This must be called for every message that was reserved and that was
	 * processed successfully.
	 *
	 * @return boolean TRUE if the message could be removed
	 */
	public function finish(MessageInterface $message) {
		$job = $this->pheanstalk->peek($message->getIdentifier());
		$this->pheanstalk->delete($job);
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