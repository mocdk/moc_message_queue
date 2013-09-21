.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _users-manual:

Developer manual
================

The extension proves a centralized way to handle asynchronous messages. The default implementation is a Beanstalk
queue, but this can easily be changed to use redis, memcache, database or any other means of transporting text-messages.

Creating your own messages
--------------------------

Many applications will probably want to create their own messages, and write handlers for that specific message. The custom
message is a class that implements the MOC\MocMessageQueue\Message\MessageInterface interface.

It is very important that these messages are kept simple, since they will be serialized and put through the underlying
messaging system (ie. beanstalk). The messages should basically just carry enough information that registered
handlers han do what they need.

As an example, do not put an extbase domain model in the message, instead add an identifier that can be used to fetch
the domain model. Besides actually making sure the message are not too big, this helps maintain properly bounded contexts.

Here is an example of a message for sending an e-mail.

.. code-block:: php

 <?php
 namespace MOC\MocMailer\Message;

 use MOC\MocMessageQueue\Message\AbstractMessage;
 use MOC\MocMessageQueue\Message\MessageInterface;

 class SendSimpleMailMessage extends AbstractMessage implements MessageInterface {

 	/**
 	 * @var string
 	 */
 	public $recipient = '';

 	/**
 	 * @var string
 	 */
 	public $sender = '';

 	/**
 	 * @var string
 	 */
 	public $message = '';

 	/**
 	 * @var string
 	 */
 	public $subject = '';

 	/**
 	 * @param string $subject
 	 * @param string $message
 	 * @param string $recipient
 	 * @param string $sender
 	 */
 	public function __construct($subject, $message, $recipient, $sender) {
 		$this->message = $message;
 		$this->recipient = $recipient;
 		$this->sender = $sender;
 		$this->subject = $subject;
 	}

 }


Registering handlers or listeners
---------------------------------

To register a handler for doing something whenever a message is received in the queue, simply wire a new slot to
the signal.

Here is an example with an inline function

.. code-block:: php

	$this->signalSlotDispatcher->connect('MOC\MocMessageQueue\Command\QueueWorkerCommandController', 'messageReceived', function(\MOC\MocMessageQueue\Message\MessageInterface $message) {
		//Do your stuff here
	});

Using inline functions is good for small stuff, but most likely you want to do something like this in you ext_localconf.php

.. code-block:: php

	/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
	$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
	$signalSlotDispatcher->connect(
	        'MOC\MocMessageQueue\Command\QueueWorkerCommandController',
	        'messageReceived',
	        'VendorKey\ExtensionName\Slots\MessageQueue',
	        'yourMethod'
	);

The yourMethod of VendorKey\ExtensionName\Slots\MessageQueue should take a MessageInterface as argument. Here is an
example that only handles messages of class MyMessage.

.. code-block:: php

	<?php
	namespace VendorKey\ExtensionName\Slots;

	class MessageQueue {

		/**
		 * @param \MOC\MocMessageQueue\Message\MessageInterface $message
		 * @return
		 */
		public function yourMethod(\MOC\MocMessageQueue\Message\MessageInterface $message) {
			if ($message instanceof MyMessage) {
				//Do more stuff here
			}
		}

	}


Implementing other backend queues
---------------------------------

If you want to implement another queue (reddis, RabbitMQ, shared memory, or something different), you should create a
class that implements the \MOC\MocMessageQueue\Queue\QueueInterface interface. See the BeanstalkQueue for inspiration and
examples.

To register your new queue, simply change the TypoScript

.. code-block:: typoscript

	config.tx_extbase {
		objects {
			MOC\MocMessageQueue\Queue\QueueInterface {
				className = YOUR_QUEUE_IMPLEMENTATION
			}
		}
	}

The TypoScript should be changed in a way that its accessible in CommandControllers (ie. in the
ext_typoscript_Setup.txt of your extensions).
