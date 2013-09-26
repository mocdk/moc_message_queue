<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}


$TCA['tx_mocmessagequeue_queue'] = array(
	'ctrl' => array(
		'title' => 'MOC Message queue',
		'label' => 'Message',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'hideTable' => 1,
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/MessageQueue.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/MessageQueue.png'
	)
);
