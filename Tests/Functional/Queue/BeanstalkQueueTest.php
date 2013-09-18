<?php
namespace MOC\MocMessageQueue\Tests\Functional\Queue;

use MOC\MocMessageQueue\Message\StringMessage;
use MOC\MocMessageQueue\Queue\BeanstalkQueue;

class BeanstalkQueueTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

	/**
	 * @var \MOC\MocMessageQueue\Queue\BeanstalkQueue
	 */
	protected $queue;


	public function setup() {
		BeanstalkQueue::$tube = 'testtube';
		$this->queue = $this->objectManager->get('MOC\MocMessageQueue\Queue\BeanstalkQueue');

		// @todo: Make configurable
		$this->pheanstalk = new \Pheanstalk_Pheanstalk('localhost');
		$this->pheanstalk->useTube('testtube');

		// flush queue:
		try {
			while (true) {
				$job = $this->pheanstalk->peekDelayed();
				$this->pheanstalk->delete($job);
			}
		} catch (\Exception $e) {
		}
		try {
			while (true) {
				$job = $this->pheanstalk->peekBuried();
				$this->pheanstalk->delete($job);
			}
		} catch (\Exception $e) {
		}
		try {
			while (true) {
				$job = $this->pheanstalk->peekReady();
				$this->pheanstalk->delete($job);
			}
		} catch (\Exception $e) {
		}
	}

	/**
	 * @test
	 */
	public function thatCountReturnsZeroOnEmptyQueue() {
		$this->assertEquals(0, $this->queue->count());
	}

	/**
	 * @test
	 */
	public function thatCountReturnsOneOnQueueWithSingleEvent() {
		$message = new StringMessage('Test payload');
		$this->queue->publish($message);
		$this->assertEquals(1, $this->queue->count());
	}

	/**
	 * @test
	 */
	public function thatTestAndReserveReturnsACorrectJob() {
		$message = new StringMessage('Test payload');
		$this->queue->publish($message);
		$newMessage = $this->queue->waitAndReserve(2);
		$this->queue->finish($newMessage);
		$this->assertEquals('MOC\MocMessageQueue\Message\StringMessage', get_class($newMessage));
	}

}