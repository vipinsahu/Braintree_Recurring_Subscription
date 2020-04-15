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
  <script>
    
  </script>
</body>
</html>