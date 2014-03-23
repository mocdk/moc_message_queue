.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _admin-manual:

Administrator Manual
====================

There is not much to do as administrator, except to make sure that the actual queue worker command is running.
The queue worker is the process that asynchronously listens to messages ad dispatches these to listeners. The actual
dispatching is done with SignalSlots as explained in the section on Architecture

The worker command is is started from the command line by issuing the cli_dispatch command

::

	typo3/cli_dispatch.phpsh extbase queueworker:start

The command will run forever, and process messages as they are available in the queue. You should start this script
with your favorite daemoenize command and make sure it always runs. And perhaps restart it once and a while to make
sure PHP won't leak all your memory.

The command can be started with an optional debugOutput parameter, which will output a line to stdout when a message
is processed or an error occurs. This output shoudl generally be piped to a logfile. Some of the messages (especialle the
errors) are also logged to the TYPO3 dev-log.

::

	typo3/cli_dispatch.phpsh extbase queueworker:start --debugOutput=true

To test that the queue is actually running, run the following command in another shell (make sure the queueworker is
started with debugOutput=TRUE)

::

	typo3/cli_dispatch.phpsh extbase queueworker:publishtestmessage "This is my testmessage"

And verify that the message TestMessage is printed to stdout where the queueworker is running.

All of the above will only work beanstalk is running on localhost (or the host specified in the extension manager configuration)

Installation
------------

The extensions is installed via the extension manager.

The default queue is a database poll based queue implementation which should work everywhere. This does not require any
configuration. We suggest switching to the beanstalk backed message queue, but this requires a running beanstalk daemon.

The hostname/ip and tube can be configured by the extension manager (only used if beanstalk is enabled).

Choosing message queue
----------------------

The extension bundles with two different queue implementations. A database driven queue that does not require any
external daemons or libraries, and a beanstalk version that requires beanstalk to be available. To use the beanstalk
version, make sure you have a running beanstalk server. Then change the setting "QueueImplementation" in the extension
manager to "Beanstalk" and verify the settings for server and tube.

The Beanstalk implementation is based on the Pheanstalk php library which is bundled with the extension. If you already
have this library included bu another extensions, you can disable the include via the extension manager.

Running under supervisord
-------------------------

To prevent leaking memory and make sure that the daemon is always running we recommend running it under supervisord or
similar.

The option maximumMessages, will make the daemon exit after a this many messages are correctly handled, and thereby
allowing supervisord to restart the daemon. It will exit with an exitcode of 9 when it reaches this amount of messages.

Here is an example of a supervisord configuration

::

 [program:fvmQueue]
 command=/usr/bin/php /PATH-TO-SITE/htdocs/typo3/cli_dispatch.phpsh extbase  queueworker:start 1000 true
 autostart=true
 exitcode=2
 user=www-data
 stdout_logfile=logs/fvmQueue.log
 stdout_logfile_maxbytes=50MB
 stdout_logfile_backups=10
 stderr_logfile=logs/fvmQueueErrors.log

This will make supervisor restart the daemon after 1000 messages, and will automatically rotate logfiles.