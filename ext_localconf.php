<?php

$pheanstalkClassRoot = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . '/Classes';
require_once($pheanstalkClassRoot . '/Pheanstalk/ClassLoader.php');
Pheanstalk_ClassLoader::register($pheanstalkClassRoot);
