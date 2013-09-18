<?php
namespace MOC\MocMessageQueue\Tests\Unit\Queue;

use MOC\MocMessageQueue\Message\StringMessage;
use MOC\MocMessageQueue\Queue\BeanstalkQueue;

class StringMessageTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

	/**
	 * @test
	 */
	public function thatPayloadIsCorrectlySet() {
		$message = new StringMessage('MOC Test');
		$this->assertEquals('MOC Test', $message->getPayload());
	}

	/**
	 * @test
	 */
	public function thatIdentiferCanBeSetAndFetched() {
		$message = new StringMessage('MOC Test');
		$message->setIdentifier('testId');
		$this->assertEquals('testId', $message->getIdentifier());
	}

}