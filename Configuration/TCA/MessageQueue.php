<?php
$TCA['tx_mocmessagequeue_queue'] = array(
	'ctrl' => $TCA['tx_mocmessagequeue_queue']['ctrl'],
	'interface' => array('showRecordFieldList' => 'data'),
	'columns' => array(
		'data' => array(
			'label' => 'Serialized event',
			'config' => array(
				'type' => 'passthrough',
			),
		)
	),
	'types' => array(
		'0' => array('showitem' => 'data')
	)
);
