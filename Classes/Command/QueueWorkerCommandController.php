<?php
namespace MOC\MocMessageQueue\Command;

use MOC\MocMessageQueue\Message\MessageInterface;
use MOC\MocMessageQueue\Message\StringMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Message queue worker
 *
 * This command can start the worker process that will listen for message in the configured queue.
 *
 * @package MOC\MocMessageQueue
 */
class QueueWorkerCommandController extends CommandController {

	/**
	 * @var \MOC\MocMessageQueue\Queue\QueueInterface
	 * @inject
	 */
	protected $queue;

	/**
	 * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 * @inject
	 */
	protected $signalSlotDispatcher;

	/**
	 * Run the queue in the background
	 *
	 * @param boolean $debugOutput If TRUE, a slot is connected that display some debug output when a message is handled
	 * @return void
	 */
	public function startCommand($debugOutput = FALSE) {
		if ($debugOutput) {
			print 'Starting up queue worker with implementation ' . get_class($this->queue) . PHP_EOL;
			$this->signalSlotDispatcher->connect(__CLASS__, 'messageReceived', function(MessageInterface $message) {
				print 'Message received: ' . get_class($message);
				if ($message instanceof StringMessage) {
					print ' - Message ' . $message->getPayload();
				}
				print PHP_EOL;
			});
		}

		while (TRUE) {
			try {
				$message = $this->queue->waitAndReserve();
				if ($message !== NULL) {
					$this->signalSlotDispatcher->dispatch(__CLASS__, 'messageReceived', array(
						'message' => $message
					));
					$this->queue->finish($message);
				}
			} catch (\Exception $exception) {
				if ($debugOutput) {
					print ' - Error handling message:' . $exception->getMessage() . PHP_EOL;
				}
				GeneralUtility::devLog('Error handling message:' . $exception->getMessage(), 'moc_message_queue');
			}
		}
	}

	/**
	 * Publish test message to queue
	 *
	 * This will publish a simple StringMessage to the queue. It is only used for test purposes.
	 *
	 * @param string $messageString The message to publish
	 * @return void
	 */
	public function publishTestMessageCommand($messageString) {
		$message = new StringMessage($messageString);
		$this->queue->publish($message);
	}

}