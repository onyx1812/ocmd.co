### ResponseCrm Package Usage Description

* Lead
	>	leadList				(To get list of leads)
	>	addLead					(To add leads)
	

* Customer
	>	addCustomer				(Before you run a transaction, a customer record must first be created in the CRM)
	>	editCustomer			(To edit customer)
	>	recurringList			(List customer�s recurrings)
	>	editRecurrings			(Edit amount, next charge due date or active status for one or many )
	>	listNotes				(List customer�s notes)
	>	addNotes				(Add customer note)
	>	addCustomerChargeback	(Mark customer as chargeback)
	>	cancelCustomerCahrgebck	(Cancel customer as chargeback)
	>	importCustomer			(Import customer)
	>	importRecurrings		(Import recurring transaction for an existing customer and recurring charge)


* Transaction
	>	addSignupTransaction	( After a customer is inserted, a transaction may be run against it)
	>	addUpsellTransaction	(After a customer is inserted, a transaction may be run against it)
	>	transactionList			(Returns a list of transactions)
	>	refundTransaction		(Refunds a transaction.)
	>	importSignupUpsell		(Import singup or upsell transaction for an existing customer)
	>	importVoidRefund		(Import void or refund transaction for an existing customer)


* Order
	>	fulfillmentListOrder	( The endpoint returns information about all transactions recorded in the CRM waiting for fulfillment)
	>	updateTracking			(Update tracking)


* Sites
	>	siteList	(The endpoint returns information about all sites, groups and product charges of a given client)



#### Usage of Lead->leadList

```
$obj = new Api('xxxxxx');//secret
$data = $obj->leadList(['datefrom' => xxxxxx,'dateto' =>xxxxxx])->get(); //Get API raw response
$data = $obj->leadList(['datefrom' => xxxxxx,'dateto' =>xxxxxx])->getInArray(); //Get API response in array
$data = $obj->leadList(['datefrom' => xxxxxx,'dateto' =>xxxxxx])->getInObject(); //Get API response in object
```
#### Usage of Customer->addCustomer
```
$obj = new Api('xxxxxx');//secret
$data = $obj->addCustomer(['SiteID' => xxxxxx,'FirstName' =>  'xxxxxx','LastName' => 'xxxxxx'])->getInArray();//Array response
$data = $obj->addCustomer(['SiteID' => xxxxxx,'FirstName' =>  'xxxxxx','LastName' => 'xxxxxx'])->getInArray(true); //Get API response in array
$data = $obj->addCustomer(['SiteID' => xxxxxx,'FirstName' =>  'xxxxxx','LastName' => 'xxxxxx'])->getInObject(); //Get API response in object
````
# Usage of Transactions->transactionList
```
$obj = new Api('xxxxxx');//secret
$data = $obj->transactionList(['dateFromUtc'=>'xxxxxx'])->getInArray();//Array response
$data = $obj->transactionList(['dateFromUtc'=>'xxxxxx'])->getInArray(true); //Get API response in array
$data = $obj->transactionList(['dateFromUtc'=>'xxxxxx'])->getInObject(); //Get API response in object
```
### Usage of Order->fulfillmentListOrder
```
$obj = new Api('xxxxxx');//secret
$data = $obj->fulfillmentListOrder(['datefrom' => 'xxxxxx'])->getInArray();//Array response
$data = $obj->fulfillmentListOrder(['datefrom' => 'xxxxxx'])->getInArray(true); //Get API response in array
$data = $obj->fulfillmentListOrder(['datefrom' => 'xxxxxx'])->getInObject(); //Get API response in object
```
### Usage of Sites->siteList
```
$obj = new Api('xxxxxx');//secret
$data = $obj->siteList(['datefrom' => 'xxxxxx'])->getInArray();//Array response
$data = $obj->siteList(['datefrom' => 'xxxxxx'])->getInArray(true); //Get API response in array
$data = $obj->siteList(['datefrom' => 'xxxxxx'])->getInObject(); //Get API response in object
```
