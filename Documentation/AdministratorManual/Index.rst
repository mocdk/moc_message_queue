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
sure PHP wont leak all your memory.

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
The default queue implementation is Beanstalk, so make sure that you have a running beanstalk server, and configure
the server name/ip-address and tube in the extension manager.

