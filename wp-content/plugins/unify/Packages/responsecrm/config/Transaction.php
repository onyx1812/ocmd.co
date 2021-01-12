<?php

return [
	'apiRules' => [
		'addSignupTransaction'	 => [
			'CustomerID'										 => 'required|integer',
			'IpAddress'											 => 'required|string',
			//'BillingAddress'	 => 'required',
			'PaymentInformation.ExpMonth'						 => 'required|string|max:2',
			'PaymentInformation.ExpYear'						 => 'required|string|max:4',
			'PaymentInformation.CCNumber'						 => 'required|string|min:12',
			'PaymentInformation.NameOnCard'						 => 'required|string',
			'PaymentInformation.CVV'							 => 'required|string',
			//'PaymentInformation.ProductGroups.0.ProductGroupKey' => 'required',
		//'PaymentInformation.ProductGroups.0.ProductGroupKey.0.CustomProducts.ProductID'	 => 'required_if:PaymentInformation.ProductGroups.0.ProductGroupKey.0.CustomProducts'
		],
		'addUpsellTransaction'	 => [
			'OrderID'						     => 'required|integer',
			'IpAddress'							 => 'required|string',
			//'ProductGroups.0.ProductGroupKey'	 => 'required'
		],
		'transactionList'		 => [
			'dateFromUtc'	 => 'required_without_all:txnGatewayID,txnCrmIDAfter,dateFromUtc,dateToUtc|integer',
			'txnGatewayID'	 => 'required_without_all:dateFromUtc,dateToUtc,txnCrmIDAfter',
			'dateToUtc'		 => 'integer',
			'txnCrmIDAfter'	 => 'numeric',
			'siteIDs'		 => 'string',
			'txnstatus'		 => 'string',
			'txntype'		 => 'string',
			'ordertype'		 => 'string',
			'txnGatewayID'	 => 'numeric',
			'customerID'	 => 'integer',
			'lastName'		 => 'string',
			'last4'			 => 'numeric',
			'processor'		 => 'numeric',
			'affid'			 => 'numeric',
			'amountfrom'	 => 'numeric',
			'amountto'		 => 'numeric'
		],
		'refundTransaction'		 => [
			'TransactionID'	 => 'required',
			'Amount'		 => 'required'
		],
		'importSignupUpsell'	 => [
			'CustomerID'						 => 'required',
			'Transaction.TransactionID'			 => 'integer',
			'Transaction.ResponseType'			 => 'required|string',
			'Transaction.ResponseText'			 => 'required|string',
			'Transaction.ProcessorID'			 => 'required|integer',
			'Transaction.DateEnteredUtc'		 => 'required',
			'Transaction.AuthCode'				 => 'string',
			'Transaction.BinNo'					 => 'string',
			//'ProductGroups.0.ProductGroupKey'	 => 'required'
		],
		'importVoidRefund'		 => [
			'TransactionID'				 => 'required|string',
			'Type'						 => 'required|string',
			'Transaction.TransactionID'	 => 'required|integer',
			'Transaction.Amount'		 => 'required|numeric',
			'Transaction.ResponseType'	 => 'required|string',
			'Transaction.ResponseText'	 => 'required|string',
			'Transaction.DateEnteredUtc' => 'required'
		]
	]
];
