.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


What does it do?
================

The extensions provides a general asynchronous message queue that can be used for all kinds of activities that can
be deferred as an asynchronous task.

It is a library, and has no frontend or backend module. The audience for this extensions (and hence this manual)
are developers that wish to execute task asynchronous.

Introduction
------------

When programming websites today, many things that are normally done during a single request can easily be deferred to a
"background" process making the request faster, and help separation concerns. A simple example of this, is a shop system
sending an e-mail. The frontend controller does not need to know how to send an e-mail, but can instead send a message
that it has an e-mail that it needs sending. This way, the webshop frontend controller will not need how to send an e-mail
 and the webrequest will probably be faster, since another process is responsible for actually sending the e-mail.

The messagequeue can also be used for more fancy stuff like Command Query Responsibility Segregation or just for passing
messages between different bounded contexts. `See this link for an explanation of CQRS`_.

.. _See this link for an explanation of CQRS: http://martinfowler.com/bliki/CQRS.html

The queue is abstract and includes a Beanstalk implementation, but can easily be changed to different implementations.

Architecture
------------

The extension is written with heavy inspiration from the `Flow jobqueue Package`_ and the matching `Beanstalk package`_
but with the difference that we have no notion of a "Job" (Eventhandler in other similar systems).
Instead the worker command will use SignalSlots to emit a Signal that other extensions can listen to in order to do
something when a message is received.

The DeveloperManual contains examples of how to register slots that handles messages, and creating new custom messages.

.. _Flow message queue Package: https://git.typo3.org/Packages/TYPO3.Queue.git
.. _Beanstalk package: https://git.typo3.org/Packages/TYPO3.Queue.Beanstalkd.git

