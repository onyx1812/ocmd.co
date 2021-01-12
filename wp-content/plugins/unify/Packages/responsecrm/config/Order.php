<?php

return [
	'apiRules' => [
		'fulfillmentListOrder'	 => [
			'datefrom' => 'required|integer'
		],
		'updateTracking'		 => [
			'Rows.TransactionID' => 'required',
			'Rows.TrackingNo'	 => 'required'
		]
	]
];
