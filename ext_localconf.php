<?php

$config = unserialize($_EXTCONF);
\MOC\MocMessageQueue\Queue\BeanstalkQueue::$server = $config['beanstalk_server'];
\MOC\MocMessageQueue\Queue\BeanstalkQueue::$tube = $config['beanstalk_tube'];

$pheanstalkClassRoot = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . '/Classes';
require_once($pheanstalkClassRoot . '/Pheanstalk/ClassLoader.php');
Pheanstalk_ClassLoader::register($pheanstalkClassRoot);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Moc\MocMessageQueue\Command\QueueWorkerCommandController';