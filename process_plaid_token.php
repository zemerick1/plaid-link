<?php  
include 'include/config.php';
include 'include/common.inc.php';

error_reporting(E_ALL);  
ini_set('display_errors', 1);  
  //Process Banking
  //Plaid-Sandbox  //N.B. This should not be used on the page like this!
  $plaid_env = 'development';

$error="";
if(!empty($_POST)) {   
	if (isset($_POST['id'])){//This is our customer ID
		if((isset($_POST['pt'])) && (isset($_POST['md']))){
			$customer_id = $_POST['id'];	
			$metadata = $_POST['md'];
			//Exchange public token for Plaid access_token
			$plaid_token = get_plaid_token($_POST['pt']);
			// build array for customer data
			$cust_data = array(
				'a_token' => $plaid_token["access_token"],
				'account_id' => $metadata['account_id'],
				'item_id' => $plaid_token["item_id"],
				'ins_id' => $metadata['institution']['institution_id'],
				'ins_name' => $metadata['institution']['name']		
			);
			plaid_addAccount($dbh, $cust_data);
			echo "Success";//The message Javascript code will look for
		
		} else {
			echo "Failed: Plaid Link Data Missing";      
			error_log("Banking Authentication Failed. Plaid Link Data Missing for " . $customer_id);
		}
	} else { error_log("Banking Authentication Failed. Customer ID Missing " . $customer_id); }
}

?>
