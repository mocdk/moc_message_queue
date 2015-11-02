<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$config = unserialize($_EXTCONF);
\MOC\MocMessageQueue\Queue\BeanstalkQueue::$server = $config['beanstalk_server'];
\MOC\MocMessageQueue\Queue\BeanstalkQueue::$tube = $config['beanstalk_tube'];

if ($config['message_queue_implementation'] === 'Beanstalk' && $config['disable_pheanstalk_import'] !== "1") {
	$pheanstalkClassRoot = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . '/Classes';
	require_once($pheanstalkClassRoot . '/Pheanstalk/ClassLoader.php');
	Pheanstalk_ClassLoader::register($pheanstalkClassRoot);
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'MOC\MocMessageQueue\Command\QueueWorkerCommandController';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
config.tx_extbase {
	objects {
		MOC\MocMessageQueue\Queue\QueueInterface {
			className = MOC\\MocMessageQueue\\Queue\\' . $config['message_queue_implementation']. 'Queue
		}
	}
}
');

