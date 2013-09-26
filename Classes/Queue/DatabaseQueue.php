<?php
namespace MOC\MocMessageQueue\Queue;

use MOC\MocMessageQueue\Message\MessageInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Database implementation of MOC message queue interface
 *
 * This class uses a database table as a queue. It uses a poll strategy where it polls the queue for any available message
 * at a fixed interval.
 *
 * @package MOC\MocMessageQueue
 */
class DatabaseQueue implements QueueInterface {

	const STATUS_NEW = 0;
	const STATUS_RESERVED = 1;

	/**
	 * Period between each poll when the calling waitAndReserve
	 */
	const SLEEP_POLL_PERIOD = 5;

	/**
	 * Publish a message in the message queue
	 *
	 * @param MessageInterface $message
	 * @return boolean Return TRUE if the message was successfully published
	 */
	public function publish(MessageInterface $message) {
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_mocmessagequeue_queue', array(
			'crdate' => time(),
			'tstamp' => time(),
			'data' => serialize($message),
			'status' => static::STATUS_NEW
		));
		return TRUE;
	}

	/**
	 * Wait until a message is available and reserve that message for processing
	 *
	 * The method will poll the db table wil a fixed interval, and return a message if one is found
	 *
	 * @param integer $timeout The timeout in seconds. NULL means forever
	 * @return \MOC\MocMessageQueue\Message\MessageInterface
	 */
	public function waitAndReserve($timeout = NULL) {
		if ($timeout !== NULL) {
			$startTime = time();
		}

		while (TRUE) {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid, data', 'tx_mocmessagequeue_queue', 'status = ' . static::STATUS_NEW, '', '', 1);
			if (count($rows) > 0) {
				$row = $rows[0];
				$message = unserialize($row['data']);
				if (!($message instanceof MessageInterface)) {
					$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_mocmessagequeue_queue', 'uid = ' . $row['uid']);
					throw new UnknownMessageException('The message queue tried to fetch a message from the queue that did not implement the correct Message interface. Message permanently removed. The de-serialized class is ' . get_class($message));
				}
				$message->setIdentifier($row['uid']);
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_mocmessagequeue_queue', 'uid = ' . $row['uid'], array('status' => static::STATUS_RESERVED));
				return $message;
			}
			sleep(static::SLEEP_POLL_PERIOD);
			if ($startTime + $timeout > time()) {
				break;
			}
		}
		return NULL;
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
		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_mocmessagequeue_queue', 'uid = ' . $message->getIdentifier());
		return TRUE;
	}

	/**
	 * Count messages in the queue
	 *
	 * @return integer
	 */
	public function count() {
		return $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('uid', 'tx_mocmessagequeue_queue', 'status' . static::STATUS_NEW);
	}

}