# Limelight API Package Usage Description

* Membership API
    * customerFindActiveProduct (Show the  valid product info.)
    * customerView                (view data about a customer in the LimeLight platform)
    * customerFind                (Retrieve a list of customer ids that match a set of criteria)
    * prospectView                (The Limelight prospectView  used view data about a prospect.)
    * prospectUpdate          (Uupdate a range of values on a prospect record)
    * prospectFind                (Retrieve a list of prospect ids)
    * orderCalculateRefund        (Help figure out the pro-rate amount for recurring orders to be refunded)
    * orderFindOverdue            (Display orders that have been declined)
    * orderRefund             (Perform a refund on the given order)
    * orderVoid                   (Perform a void on the given order)
    * orderForceBill          (Perform a force bill on the given order)
    * orderUpdateRecurring        (Update the recurring status of an order)
    * orderFind                 (Find orders and get a result of order Ids)
    * orderFindupdated          (Return orders that have been modified since creation)
    * orderUpdate               (Update a range of values on an order)
    * orderView                 (Display critical information regarding the order and its associated data)
    * orderReprocess            (Perform a reprocess on the given order)
    * offerView                 (Display offer details)
    * campaignView              (Get data about a campaign, based on the campaign_id submitted)
    * campaignFindActive        (Display all campaigns currently active)
    * productAttributeIndex     (Display attribute information related to several products)
    * productBundleIndex        (Retrieve information about a specific bundle)
    * productCopy               (Copy existing product)
    * subscriptionUpdate        (Subscription status of an order)
    * upsellStopRecurring       (Stop upsell product recurring)
    * repostToFulfillment       (Send orders to the fulfillment provider)
    * getAlternativeProvider    (Retrieve redirection information for alternative payment providers)
    * shippingMethodView        (View data about a shipping method)
    * shippingMethodFind        (Retrieve a list of shipping ids that match a set of criteria)
    * couponValidate            (Check if a promo code that was entered is still valid to apply the discount)
    * gatewayView               (View the details of a particular or list of gateways)
    * skipNextBilling           (Skip the next billing cycle on an existing subscriptiont)
    * paymentRouterView         (Display desired payment router(s) and the related gateway details)
    * subscriptionOrderUpdate   (Update an existing order's next recurring details)


* Transaction API
    * newProspect                (Create a new prospect record.)
    * newOrder                   (Creates New Order)
    * newOrderCardOnFile         (Supports all the existing parameters of the NewOrder method)
    * threeDRedirect             (Redirects the customer to their personal bank URL for 3D Secure payments)
    * authorizePayment           (Utilizes the billing and card data fields used by NewOrder)



* Member API
    * validateCredentials        (Checking API Credentials Valid Or Not.)




#Usage of customerView
```
use CodeClouds\Limelight\API;

$obj = new API('https://xxx.com','xxxxx','xxxxxx');//end point , username, password
```
```

$data = $obj->customerView(['customer_id' => 'xxx'])->get(); //Get API raw response

$data = $obj->customerView(['customer_id' => 'xxx'])->get(true); //Get API raw response with payload info

$data = $obj->customerView(['customer_id' => 'xxx'])->getInArray(); //Get API response in array

$data = $obj->customerView(['customer_id' => 'xxx'])->getInArray(true); //Get API response in array with payload info

$data = $obj->customerView(['customer_id' => 'xxx'])->getInObject(); //Get API response in object

$data = $obj->customerView(['customer_id' => 'xxx'])->getInObject(true); //Get API response in object with payload info
```

#Usage of orderFind

```
$data = $obj->orderFind(['order_id' => 'xxx','campaign_id' => 'xxx','start_date' => 'xxx','end_date' => 'xxx','criteria' => 'xxx'])->get(); //Get API raw response


$data = $obj->orderFind(['order_id' => 'xxx','campaign_id' => 'xxx','start_date' => 'xxx','end_date' => 'xxx','criteria' => 'xxx'])->get(true); //Get API raw response with payload info


$data = $obj->orderFind(['order_id' => 'xxx','campaign_id' => 'xxx','start_date' => 'xxx','end_date' => 'xxx','criteria' => 'xxx'])->getInArray(); //Get API response in array

$data = $obj->orderFind(['order_id' => 'xxx','campaign_id' => 'xxx','start_date' => 'xxx','end_date' => 'xxx','criteria' => 'xxx'])->getInArray(true); //Get API response in array with payload info


$data = $obj->orderFind(['order_id' => 'xxx','campaign_id' => 'xxx','start_date' => 'xxx','end_date' => 'xxx','criteria' => 'xxx'])->getInObject(); //Get API response in object


$data = $obj->orderFind(['order_id' => 'xxx','campaign_id' => 'xxx','start_date' => 'xxx','end_date' => 'xxx','criteria' => 'xxx'])->getInObject(true); //Get API response in object with payload info

```
