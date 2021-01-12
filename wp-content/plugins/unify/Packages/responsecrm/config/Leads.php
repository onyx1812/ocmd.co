<?php

return [
	'apiRules' => [
		'leadList'	 => [
			'datefrom'	 => 'required|integer',
			'dateto'	 => 'required|integer'
		],
		'addLead'	 => [
			'SiteID'			 => 'required|integer',
			'NextChargeDueDate'	 => 'required|integer',
			'PaymentInformation' => 'required'
		]
	]
];
