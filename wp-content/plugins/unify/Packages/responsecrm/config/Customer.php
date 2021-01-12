<?php

return [
	'apiRules' => [
		'addCustomer'				 => [
			'SiteID' => 'required|integer',
			'Email'	 => 'email'
		],
		'editCustomer'				 => [
			'FirstName'	 => 'required',
			'LastName'	 => 'required',
			'Email'		 => 'email'
		],
		'editRecurrings'			 => [
			'CustomerIDs'					 => 'required',
			'RecurringChargeID'				 => 'required',
			'NewValues.Amount'				 => 'required_without_all:NewValues.NextChargeDueDateUtc,NewValues.PushDueDateDays,NewValues.IsActive|numeric',
			'NewValues.NextChargeDueDateUtc' => 'required_without_all:NewValues.Amount,NewValues.PushDueDateDays,NewValues.IsActive',
			'NewValues.PushDueDateDays'		 => 'required_without_all:NewValues.NextChargeDueDateUtc,NewValues.Amount,NewValues.IsActive|integer',
			'NewValues.IsActive'			 => 'required_without_all:NewValues.NextChargeDueDateUtc,NewValues.Amount,NewValues.NextChargeDueDateUtc|boolean',
		],
		'recurringList'				 => [],
		'listNotes'					 => [],
		'addNotes'					 => [
			'Note' => 'required'
		],
		'addCustomerChargeback'		 => [],
		'cancelCustomerCahrgebck'	 => [
			'CustomerIDs' => 'required'
		],
		'importCustomer'			 => [
			'SiteID'					 => 'required|integer',
			'Email'						 => 'required|email',
			'DateEnteredUtc'			 => 'required',
			'CreditCard.ExpMonth'		 => 'required|string|max:2',
			'CreditCard.ExpYear'		 => 'required|string|max:4',
			'CreditCard.CcLast4'		 => 'required|string|max:4',
			'CreditCard.CardType'		 => 'required|string',
			'ShippingAddress'			 => 'required',
			'ShippingAddress.FirstName'	 => 'string|max:50',
			'ShippingAddress.LastName'	 => 'string|max:50',
			'ShippingAddress.Address1'	 => 'string|max:50',
			'ShippingAddress.Address2'	 => 'string|max:50',
			'ShippingAddress.City'		 => 'string|max:50',
			'ShippingAddress.State'		 => 'string|max:50',
			'ShippingAddress.CountryISO' => 'string|max:2',
			'ShippingAddress.Phone'		 => 'numeric',
			'ShippingAddress.ZipCode'	 => 'string|max:10',
			'BillingAddress'			 => 'required',
			'BillingAddress.FirstName'	 => 'string|max:50',
			'BillingAddress.LastName'	 => 'string|max:50',
			'BillingAddress.Address1'	 => 'string|max:50',
			'BillingAddress.Address2'	 => 'string|max:50',
			'BillingAddress.City'		 => 'string|max:50',
			'BillingAddress.State'		 => 'string|max:50',
			'BillingAddress.CountryISO'	 => 'string|max:2',
			'BillingAddress.Phone'		 => 'numeric',
			'BillingAddress.ZipCode'	 => 'string|max:10',
		//'MDFs.0.Name'				 => 'required|string',
		//'MDFs.0.Value'				 => 'required|string',
		//'Notes.0.Message'			 => 'required',
		//'Notes.0.DateEnteredUtc'	 => 'required'
		],
		'importRecurrings'			 => [
			'RecurringID'				 => 'required|integer',
			'Transaction.TransactionID'	 => 'integer',
			'Transaction.ResponseType'	 => 'required|string',
			'Transaction.ResponseText'	 => 'required|string',
			'Transaction.ProcessorID'	 => 'required|integer',
			'Transaction.DateEnteredUtc' => 'required',
			'Transaction.AuthCode'		 => 'string',
			'Transaction.BinNo'			 => 'string',
		]
	]
];
