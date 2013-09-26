#
# Table structure for table 'tx_mocmessagequeue_queue'
#
CREATE TABLE tx_mocmessagequeue_queue (
	`uid` int(11) NOT NULL auto_increment,
	`pid` int(11) DEFAULT '0' NOT NULL,
	`tstamp` int(11) DEFAULT '0' NOT NULL,
	`crdate` int(11) DEFAULT '0' NOT NULL,
	`cruser_id` int(11) DEFAULT '0' NOT NULL,
	`data` longblob NOT NULL,
	`status` int(3) DEFAULT '0' NOT NULL,

	PRIMARY KEY (`uid`),
	KEY crdate (`crdate`),
	KEY parent (`pid`)
) ENGINE=InnoDB;