<?php
  include 'include/config.php';
  include 'include/common.inc.php';
?>
<html lang="en">  
<head>
  <meta charset="utf-8">
  <title>PHP / Plaid-Link</title>
  <meta name="description" content="">
  <meta name="author" content="">
 
  <script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
  <script
  src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
 
  <script type="text/javascript">
 
$(document).ready(function() { 
  // Trigger the standard Institution Select view
  document.getElementById('linkButton').onclick = function() {
    linkHandler.open();
  };  
});        
 
  </script>
 
</head>  
<body>  
  <p>Please link your <strong>primary bank account</strong>.</p>
  <button id="linkButton">Link Your Bank Account</button>              
<p>Current Linked Accounts</p>
<table style="width=100%">
  <tr>
        <th>Name</th>
        <th>Balance (Available)</th>
        <th>Type</th>
  </tr>
<?php 
	$accounts = getAllAccounts($dbh); 
	print_accounts($accounts, $plaid_client_id, $plaid_secret);
	
?>	
</table>
 
  <script>
  var linkHandler = Plaid.create({
    selectAccount: true,
    env: '<?php echo $plaid_env ?>',
    apiVersion: 'v2',
    clientName: 'Client Name',
    key: '<?php echo $plaid_public ?>',
    product: ['auth', 'transactions'],
    //webhook: 'https://myurl.com/webhooks/p_responses.php',
    onLoad: function() {
      // The Link module finished loading.
    },
    onSuccess: function(public_token, metadata) {
    // The onSuccess function is called when the user has successfully
    // authenticated and selected an account to use.     
      $.post( 'process_plaid_token.php', {pt:public_token,md:metadata,id:"<?php echo $customer_id;?>"}, function( data ) {                        
          console.log("data : "+data);
           if (data=="Success"){              
              console.log("Success");
             // window.location.replace("thankyou.php");//Let users know the process was successful 
           }
	   // else{
           //  console.log("Error");
           //  window.location.replace("error.php");//Let users know the process failed
           //}
        });    
    },
    onExit: function(err, metadata) {
      // The user exited the Link flow. This is not an Error, so much as a user-directed exit   
      if (err != null) {
        console.log(err);
        console.log(metadata);        
      }
    },
  });
  </script>
 
<!-- End Document  
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
</body>  
</html> 
<?php
function print_accounts($accounts, $plaid_client_id, $plaid_secret) {
foreach ($accounts as $k=>$v) {
error_log($plaid_client_id);
 $data = array(
            "client_id" => $plaid_client_id,
            "secret" => $plaid_secret,
            "access_token"=>$v['a_token']
        );
        $plaid_url = 'https://development.plaid.com';
        $data_fields = json_encode($data);

        //initialize session
        $ch=curl_init($plaid_url . "/accounts/get");

        //set options
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($data_fields))
        );

        //execute session
	$account_json = curl_exec($ch);
        $acct = json_decode($account_json,true);
	
        foreach ($acct['accounts'] as $key => $value) {
		echo "<tr>";
		echo "<td>{$value['name']}</td>";
                $amt = $value['balances']['current'];
                echo "<td><font color=\"green\">$amt</font></td>";
                echo "<td>{$value['subtype']}</td>";
                echo "</tr>";
}

        //check for errors
        if(isset($acct['error_code'])){
          error_log("Plaid Error Message: " . $balance_json);
        }
        //close session

  }
}
?>
