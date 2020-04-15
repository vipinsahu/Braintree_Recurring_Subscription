# Braintree Recurring Subscription

Braintree's recurring billing, you can charge your customers automatically in monthly increments.

## Dependencies

The following PHP packages are required and we can install via Composer. 
1. `braintree/braintree_php` | The Braintree PHP library provides integration access to the Braintree Gateway
2. `nubs/random-name-generator` | A library to create interesting, sometimes entertaining, random names

PHP version >= 7.2 is required. The Braintree PHP SDK is tested against PHP versions 7.3 and 7.4.

## Configuration
API credentials are unique account identifiers that must be added to your code before you can process payments via the API.

    <?php 
    $this->gateway =  new Braintree_Gateway([
	 'environment'  => 'sandbox',
	 'merchantId' 	=> 'XXXXXXXXXXX',
	 'publicKey' 	=> 'XXXXXXXXXXX',
	 'privateKey' 	=> 'XXXXXXXXXXX'
	]);
    ?>
The environment specifies where requests via the API should be directed – sandbox or production. Because you have a different set of API keys for each environment, you'll need to update your code depending on which environment you're working in.

## Prerequisites
> You must have to create `Plan` in the Braintree site

> You must have to create `addons` and `discount` in the Braintree

   - `addons` will be created with the *Add-on ID* `UpgradePlanAddOn` 
   - `discount` will be created with the *Discount ID* `DowngradePlanDiscount` 

## Usage

With the help of this repository, user can buy a subscription and update subscription in the mid of billing cycle. Below features that we have developed yet.
1. Create `customer`
2. Create/Update `subscription`
3. Create transaction of the `subscription`
4. Add `Addons` or `discounts`

## Braintree Documentation
[Official documentation](https://developers.braintreepayments.com/start/hello-server/php)


## Quick Start Example
- To create a new subscription by running the below code or after successful installation you would able to access create new `subscription` page by visiting the URL `http://localhost/BraintreeRecurringSubscription`

```html
<?php
require_once 'function.php';
 
//Create class object to access methods 
$gateway = new BrainTreeSubscription();

//Generate and get client token
$clientToken = $gateway->getClientToken();

//Generate and get random name for testing only
$name = $gateway->generateRandomName();
?>
<!doctype html> 
<html>
<head>
   <title>Braintree-Demo</title>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
   <script src="https://js.braintreegateway.com/web/dropin/1.8.1/js/dropin.min.js"></script>
   <script src="https://js.braintreegateway.com/v2/braintree.js"></script>
   <link href="style.css" rel="stylesheet">
   <script>
      var clientToken = "<?=$clientToken?>"; 
      braintree.setup(clientToken, "dropin", {
      	container: "dropin-container",
      });
   </script>
</head>
<body>
   <div class="container">
      <form method="post" id="paymentForm" action="subscription_create.php">
         <h4>Payment details</h4>
         <ul>
            <li>
               <label for="plan">Select Plan </label>
               <select name="plan" id="plan">
                  <option value="">--Select--</option>
                  <?php foreach($gateway->getPlans() as $plan){ ?>
                  <option value="<?php echo $plan->id; ?>"><?php echo $plan->name.'('.$plan->price.')'; ?></option>
                  <?php } ?>
               </select>
            </li>
            <li>
               <label for="firstname">First name </label>
               <input type="text" name="firstname" id="firstname" maxlength="20" value="<?php echo $name[0] ?>"> 
            </li>
            <li>
               <label for="lastname">Last Name</label>
               <input type="text" name="lastname" id="lastname"  value="<?php echo $name[1] ?>"> 
            </li>
            <li style="clear:both;">
               <div id="dropin-container"></div>
            </li>
            <li style="clear:both;">
               <input type="submit" value="Pay Now" id="paymentButton" />
            </li>
         </ul>
      </form>
   </div>
</body>
</html>
```
Whenevere we run the above code or access the page via URL then the page will look like below image.

![](https://github.com/vipinsahu/Braintree_Recurring_Subscription/blob/master/images/braintree-demo.png)

After submission of the above form, system will create below entities.
1. > Cusromer

2. > Customer Payment Method Token

3. > Subscription

- To *update* a subscription by running the below code or you would able to access update a `subscription` page by visiting the URL `http://localhost/BraintreeRecurringSubscription/subscription_update.php`


```html
<?php
   require_once 'function.php';
   
   //Create class object to access methods 
   $gateway = new BrainTreeSubscription();
   
   ?>
<!doctype html> 
<html>
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Braintree Update Demo</title>
   <link href="style.css" rel="stylesheet">
</head>
<body>
   <div class="container">
      <form method="post" id="paymentForm" action="subscription_updated_response.php">
         <h4>Update Subscription</h4>
         <ul>
            <li>
               <label for="subscriptionid">Subscription ID</label>
               <input type="text" name="subscriptionId" id="subscriptionId" value="dc23hb"> 
            </li>
            <li>
               <label for="plan">Select Plan </label>
               <select name="plan" id="plan">
                  <option value="">--Select--</option>
                  <?php foreach($gateway->getPlans() as $plan){ ?>
                  <option value="<?php echo $plan->id.':'.$plan->price; ?>"><?php echo $plan->name.'('.$plan->price.')'; ?></option>
                  <?php } ?>
               </select>
            </li>
            <li style="clear:both;">
               <input type="submit" value="Pay Now" id="paymentButton" />
            </li>
         </ul>
      </form>
   </div>
</body>
</html>

```
Whenevere we run the above code or access the page via URL then the page will look like below image.

![](https://github.com/vipinsahu/Braintree_Recurring_Subscription/blob/master/images/braintree-update-demo.png)
