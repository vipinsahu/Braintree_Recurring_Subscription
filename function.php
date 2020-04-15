<?php
require_once 'vendor/autoload.php';


class BrainTreeSubscription{


	public $gateway;
	public $prorateCharges = true;
	/*
	* This method is used to make connection using the credentials
	* Param  : None
	* Return : None	
	*/
	public function __construct(){

		try {
			$this->gateway =  new Braintree_Gateway([
			  'environment' => 'sandbox',
			  'merchantId' 	=> 's3y9sj9p6hjzdx7z',
			  'publicKey' 	=> '9f8wzd63gq3cwz98',
			  'privateKey' 	=> '474be2d792aae4382a83e5078662d8d6'
			]);

		} catch (Exception $e) {
		  	return $e->getMessage();
		}
	}

	/*
	* This method is used to get object of connection
	* Param  : None
	* Return : object
	*/
	public function connectGateway(){
		return $this->gateway;
	}

	/*
	* This method is used to generate Client token
	* Param  : None
	* Return : string	
	*/
	public function getClientToken(){
		return $this->gateway->clientToken()->generate();
	}

	/*
	* This method is used to get all the plans created in BrainTree site
	* Param  : None
	* Return : array	
	*/
	public function getPlans(){
		return $this->gateway->plan()->all();
	}

	/*
	* This method is used to generate random name for testing only
	* Param  : None
	* Return : array
	*/
	public function generateRandomName(){ 
		return explode(" ", \Nubs\RandomNameGenerator\All::create()->getName());
	}

	/*
	* This method is used to create customer
	* Param  : None
	* Return : None	
	*/
	public function createCustomer($data){
		return $this->gateway->customer()->create([
		    'firstName' 		=> $data['firstname'],
		    'lastName' 			=> $data['lastname'],		    
		    'paymentMethodNonce'=> $data['payment_method_nonce']
		]);
	}

	/*
	* This method is used to create customer's subscription
	* Param  : None
	* Return : None	
	*/
	public function createSubscriptionForCustomer($customerPaymentMethodToken, $data){
		return $this->gateway->subscription()->create([
		      'paymentMethodToken' => $customerPaymentMethodToken,
		      'planId' => $data['plan'],
	    ]);
	}

	/*
	* This method is used to create customer and subscription
	* Param  : array
	* Return : object
	*/
	public function createCustomerAndSubscription($data){

		try{
			$result = $this->createCustomer($data);

			if ($result->success) {
			    //Get customer ID
			    $customerId = $result->customer->id;

			    //Get customer PaymentMethodToken
			    $customerPaymentMethodToken = $result->customer->paymentMethods[0]->token;

			    //Create subscription
			    if(!empty($customerPaymentMethodToken) && !empty($data['plan'])){
				    $result = $this->createSubscriptionForCustomer($customerPaymentMethodToken, $data);

					return $result;
				}
			} else {
			    foreach($result->errors->deepAll() AS $error) {
			        echo($error->code . ": " . $error->message . "\n");
			    }
			}	
		} catch (\Exception $ex) { 
	        return $ex->getMessage();
	    }		
	}

	/*
	* This method is used to calculate days in two dates
	* Param  : array
	* Return : object
	*/
	public function getDays($startDate, $endDate){
		return date_diff(date_create($startDate),date_create($endDate))->format("%a");
	}

	/*
	* This method is used to format date
	* Param  : array
	* Return : object
	*/
	public function formatDate($dateObject){
		return $dateObject->format('Y-m-d');
	}

	/*
	* This method is used to format amount
	* Param  : array
	* Return : object
	*/
	public function formatAmount($amount){
		return number_format((float)$amount, 2, '.', '');
	}

	/*
	* This method is used to get formatted date
	* Param  : array
	* Return : object
	*/
	public function chargeAccordingToType($oldPlanPrice, $newPlanPrice, $leftDaysInBillingCycle, $totalDaysInBillingCycle){

		if($oldPlanPrice <  $newPlanPrice){
			$data = [
				'type' 	=> 'addOns',
				'price'	=> ($newPlanPrice - $oldPlanPrice) * ($leftDaysInBillingCycle / $totalDaysInBillingCycle)
			];
		}else{
			$data = [
				'type' 	=> 'discounts',
				'price'	=> ($oldPlanPrice - $newPlanPrice) * ($leftDaysInBillingCycle / $totalDaysInBillingCycle)
			];
		}
		return $data;
	}

	/*
	* This method is used to get subscription by ID
	* Param  : array
	* Return : object
	*/
	public function prepareChargeData($subscription, $leftDaysInBillingCycle, $totalDaysInBillingCycle, $newPlanPrice){

		$chagre = $this->chargeAccordingToType($subscription->price, $newPlanPrice, $leftDaysInBillingCycle, $totalDaysInBillingCycle);
		return [
			'price'				=> $this->formatAmount($chagre['price']),
			'type'				=> $chagre['type'],
			'planPrice'			=> $newPlanPrice,
			'subscribedPrice'	=> $subscription->price,
		];
	}

	/*
	* This method is used to format amount
	* Param  : array
	* Return : object
	*/
	public function getSubscriptionById($subscriptionId){
		return $this->gateway->subscription()->find($subscriptionId);
	}

	/*
	* This method is used to get subscription by ID
	* Param  : array
	* Return : object
	*/
	public function prepareDataToSubscription($subscriptionId, $newPlanPrice){

		//Get subscription information by ID
		$subscription 				= $this->getSubscriptionById($subscriptionId);

		//Prepare data to update subscription
		$subscribedDate 			= $this->formatDate($subscription->billingPeriodStartDate);
		$nextBillingCycleDate 		= $this->formatDate($subscription->nextBillingDate);
		
		$leftDaysInBillingCycle		= $this->getDays(date('Y-m-d'), $nextBillingCycleDate);
		$totalDaysInBillingCycle 	= $this->getDays($subscribedDate, $nextBillingCycleDate);
		return [
			'subscribedPrice' 			=> $subscription->price,
			'subscribedDate' 			=> $subscribedDate,
			'nextBillingCycleDate' 		=> $nextBillingCycleDate,
			'totalDaysInBillingCycle'	=> $totalDaysInBillingCycle,
			'leftDaysInBillingCycle'	=> $leftDaysInBillingCycle,
			'chargableData'				=> $this->prepareChargeData(
				$subscription, $leftDaysInBillingCycle, $totalDaysInBillingCycle,$newPlanPrice
			),
		];
	}

	/*
	* This method is used to prepare addOns or  discounts array format to update
	* Param  : $subscription, $newPlanId
	* Return : array
	*/
	public function createAddOnOrDiscount($subscription, $newPlanId){

		if($subscription['chargableData']['type'] == 'addOns'){
			$updateData = [
			    'addOns' => array(
			        'add' => array([
			                'amount' 			=> $subscription['chargableData']['price'],
			                'inheritedFromId' 	=> 'UpgradePlanAddOn'// Already created on BrainTree panel
			            ]
			        )
			    )
			];
		}elseif($subscription['chargableData']['type'] == 'discounts'){
			$updateData = [
			    'discounts' => array(
			        'add' => array([
			                'amount' 			=> $subscription['chargableData']['price'],
			                'inheritedFromId' 	=> 'DowngradePlanDiscount'// Already created on BrainTree panel
			            ]
			        )
			    )
			];
		}	
		return $updateData + ['options' => ['prorateCharges' => $this->prorateCharges], 'planId' => $newPlanId];	
	}

	/*
	* This method is used to update subscription
	* Param  : array
	* Return : object
	*/
	public function updateSubscription($data){
		try {
			$subscriptionId = $data['subscriptionId'];
			$newPlanData 	= explode(':', $data['plan']);//[0=>PlanID, 1=>PlanPrice]		
			$newPlanId		= $newPlanData[0];
			$newPlanPrice	= $newPlanData[1];

			//Prepare data to update subscription
			$subscription 	= $this->prepareDataToSubscription($subscriptionId, $newPlanPrice);
	 	
	 		$result = $this->gateway->subscription()->update($subscriptionId, $this->createAddOnOrDiscount($subscription, $newPlanId));
	 		if($result->success){
	 			return $result;
	 		}
	 		return $result->message;
 		} catch (\Exception $ex) { 
	        return $ex->getMessage();
	    }
	}

}
?>