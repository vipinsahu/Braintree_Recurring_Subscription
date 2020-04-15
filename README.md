# Braintree Recurring Subscription

Braintree's recurring billing, you can charge your customers automatically in monthly increments.

## Dependencies

The following PHP packages are required and we can install via Composer. 
1. `braintree/braintree_php` | The Braintree PHP library provides integration access to the Braintree Gateway
2. `nubs/random-name-generator` | A library to create interesting, sometimes entertaining, random names

PHP version >= 7.2 is required. The Braintree PHP SDK is tested against PHP versions 7.3 and 7.4.

## Usage

With the help of this repository, user can buy a subscription and update subscription in the mid of billing cycle. Below features that we have developed yet.
1. Create `customer`
2. Create/Update `subscription`
3. Create transaction of the `subscription`
4. Add `Addons` or `discounts`

## Braintree Documentation
[Official documentation](https://developers.braintreepayments.com/start/hello-server/php)


## Quick Start Example
- To create a new subscription run the below code or URL (http://localhost/BraintreeRecurringSubscription)
####HTML code

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
      	paypal: {
              flow: 'vault'
           	}
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

