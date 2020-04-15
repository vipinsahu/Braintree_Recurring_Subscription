<?php
require_once 'function.php';

//Create class object to access methods 
$gateway = new BrainTreeSubscription();

?>
<!doctype html> 
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