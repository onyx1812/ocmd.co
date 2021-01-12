<?php

return [
	'NEW_CONNECTION' => 'A new connection <span>“{$title} #{$pid}”</span> has been created.',
	'EDIT_CONNECTION' => '<span>“{$title} #{$pid}”</span> has been updated.',
	'active_connection_deleted' => '<span>“{$title} #{$pid}”</span> has been deleted.',
	'connection_restore' => '<span>“{$title} #{$pid}”</span> has been restored.',
	'bulk_connection_restore' => '<span>“{$count}”</span> connections have been restored.',
	'FILES' => [
		'INVALID' => 'Please select a valid .csv file to upload.',
		'VALID' => 'Successfully uploaded.',
	],
	'COMMON' => [
		'ERROR' => 'Something went wrong!.',
		'PAYMENT_FAILED' => 'Payment Failed! Please make sure you have entered the correct information.',
	],
	'PRODUCT_MAP' => [
		'SUCCESS' => 'Successfully set the mapping for the products.'
	],
	'VALIDATION' => [
		'CREATE_CONNECTION' => [
			'POST_TITLE' => 'Connection Name is a required field',
			'UNIFY_CONNECTION_CRM' => 'CRM is a required field',
			'UNIFY_CONNECTION_ENDPOINT' => 'Endpoint is a required field',
			'UNIFY_CONNECTION_API_USERNAME' => 'Username is a required field',
			'UNIFY_CONNECTION_API_PASSWORD' => 'Password is a required field',
			'UNIFY_CONNECTION_CAMPAIGN_ID' => 'Campaign ID is a required field',
		],
		'REQUEST_UNIFY_PRO' => [
			'FULL_NAME' => 'Full Name is a required field.',
			'COMPANY_NAME' => 'Company Name is a required field.',
			'EMAIL_ADDRESS' => 'Email is a required field.',
			'EMAIL_ADDRESS_INVALID' => 'Please provide a valid email address.',
			'PHONE_NUMBER' => 'Phone is a required field.',
			'PHONE_NUMBER_INVALID' => 'Please provide a valid phone number.',
			'COMMENT' => 'Comment is a required field.',
		],
	],
	'REQUEST_UNIFY_PRO' => [
		'MAIL_SENT' => 'You request has been sent successfully.'
	],
	'SETTINGS' => [
		'SAVE' => 'Unify settings has been updated successfully.'
	],
	'CONNECTION' => [
		'UNDO_CONNECTION' => 'Revert to previous connection <span>“{$title} #{$pid}”</span> as active connection.',
		'CONNECTION_ACTIVATED' => 'Successfully set <span>“{$title} #{$pid}”</span> as active connection.',
		'CONNECTION_DELETED' => '<span>“{$title} #{$pid}”</span> has been deleted.',
		'BULK_CONNECTION_DELETED' => '<span>“{$count}”</span> connections have been deleted.',
		'UNDO_CONNECTION_DELETED' => '<span>“{$title} #{$pid}”</span> connections has been revert back to publish.',
		'UNDO_CONNECTION_BULK_DELETED' => '<span>“{$count}”</span> connections has been revert back to publish.',
	]
];
