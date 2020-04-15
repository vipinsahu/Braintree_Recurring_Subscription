<?php
require_once 'function.php';

//Create class object to access methods 
$gateway = new BrainTreeSubscription();

//Create customer and subscription
$result = $gateway->updateSubscription($_POST);
?>
<!doctype html> 
<head>
  <title>Response Object</title>   
  <link href="style.css" rel="stylesheet"> 
</head>
<body>
    <div class="container">     
       <div id="paymentForm" style="width: 800px">
      <h4>Response Object</h4>
      <pre>
        <code class="html">
          <?php print_r($result); ?>
        </code>
      </pre>
    </div>
    </div>
</body>
</html>