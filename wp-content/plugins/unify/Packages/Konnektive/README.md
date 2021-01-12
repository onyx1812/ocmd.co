<!-- Konnective Package Usage Description-->
# Contains four classes i.e. Order,Customer,Transactions and Campaign

* Order
    * orderQuery          (To get returns information about existing orders)
    * importLeads         (Import Lead API allows you to add new leads to the CRM )
    * preauth             (Preauth Order API allows you to preauth a credit card for 1.00 on new)
    * importOrder         (To place new order)
    * importUpsale        (Upsale API allows you bill and add upsales to existing orders)
    * confirm             (Confirm Order API will immediately send confirmation auto responder emails)
    * refund              (Refund Order API will allow you to issue partial and full refunds against)
    * cancel              (To cancel an order)
    * qa                  (Order QA API allows you to approve or decline orders that are pending)
    * fulfillmentUpdate   (Update Fulfillment API will allow you to update fulfillment information)
    * rerun               (Rerun Declined Sale API will allow you to retry billing of a declined)
    * salestax            (Order Sales Tax API allows you to compute tax for new orders)

* Customer
    * customerQuery       (Query Customer API returns information about existing Customers)
    * addnote             (Add Customer Note API will allow you to add a note to a customer account)
    * update              (Update Customer API will allow you to update customer information)
    * history             (Query Customer History API returns a customer's about existing Customers)
    * blacklist           (Blacklist API allows you to add a new blacklist entry)
    * contracts           (Query Customer Contracts API returns the base64-encoded customer contract)

* Transaction
    * transactionsQuery   (Query Transaction API returns information about all transactions recorded)
    * cbdataList          (Transaction Range Select API returns the IDs of successful transactions)
    * cbdataQuery         (Composite Data Query API returns a data set combining information from)
    * updateTransaction   (Update Purchase API will allow you to update transaction information.)
    * refundTransaction   (Transaction Refund API will allow you to issue partial and full refunds)
    * purchaseQuery       (Query Order API returns information about existing orders)
    * updatePurchase      (Purchase API will allow you to update purchase information)
    * cancelPurchase      (Cancel Purchase API will cancel an existing recurring purchase)
    * refundPurchase      (Purchase Refund API will allow you to issue partial and full refunds)

* Campaign
    * campaignQuery       (Query Campaigns API returns information about Campaigns)
    * midSummary          (Query Mid Summary Report API returns summary report of mid information)
    * retention           (Query Retention Report API returns information about the retention report)


#Usage of Order->orderQuery
```
$obj = new Api('https://xxx.com','xxxxx','xxxxxx');//end point , username, password

$data = $obj->orderQuery(['startDate' => 'xxx','endDate' => 'xxx'])->get(); //Get API raw response

$data = $obj->orderQuery(['startDate' => 'xxx','endDate' => 'xxx'])->getInArray(); //Get API response in array

$data = $obj->orderQuery(['startDate' => 'xxx','endDate' => 'xxx'])->getInObject(); //Get API response in object

```

#Usage of Customer->customerQuery
```
$obj = new Api('https://xxx.com','xxxxx','xxxxxx');

$data = $obj->customerQuery(['startDate' => 'xxx','endDate' => 'xxx'])->getInArray();//Array response

$data = $obj->customerView(['startDate' => 'xxx','endDate' => 'xxx'])->getInArray(true); //Get API response in array with payload info
```

#Usage of Transactions->transactionsQuery
```
$obj = new Api('https://xxx.com','xxxxx','xxxxxx');

$data = $obj->transactionsQuery(['startDate' => 'xxx','endDate' => 'xxx']))->getInArray();//Array response
```
#Usage of Campaign->campaignQuery
```
$obj = new Api('https://xxx.com','xxxxx','xxxxxx'))->getInArray();

$data = $obj->campaignQuery([]);//Array response
```
#Usage of Campaign->midSummary
```
$obj = new Api('https://xxx.com','xxxxx','xxxxxx');

$data = $obj->midSummary([]);//Array response
```
