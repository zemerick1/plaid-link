<?php
	include 'include/config.php';
?>
<!doctype html>
<META HTTPEQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<meta httpequiv="expires" content="0" />
<html lang="en">
<head>
  <title>  </title>
</head>
<body>
<table style="width=100%">
  <tr>
	<th>Name</th>
	<th>Amount</th>
	<th>Date</th>
  </tr>
<?php
 $data = array(
            "client_id" => $plaid_client_id,
            "secret" => $plaid_secret,
            "access_token"=> 'FIX THIS SHOULD PULL FROM DB',
	    "start_date" => '2018-12-30',
	    "end_date" => '2019-01-04'
	);
	$plaid_url = $plaid_url;  
        $data_fields = json_encode($data);

        //initialize session
        $ch=curl_init($plaid_url . "/transactions/get");

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
        $balance_json = curl_exec($ch);
        $balance = json_decode($balance_json,true);
	foreach ($balance['transactions'] as $key => $value) {
		echo "<tr><td>";
		echo $value['name'];
		echo "</td><td>";
		$amt = $value['amount'];
		if (strpos($amt, '-') !== false) { 
			$amt = str_replace('-','',$amt);
			echo "<font color=\"green\">$amt</font>"; 
			}
			else { echo "<font color=\"red\">$amt</font>"; }
		#echo $value['amount'];
		echo "</td>";
		echo "<td>";
		echo $value['date'];
		echo "</td></tr>";
}
		echo "</table></body></html>"; 

        //check for errors
        if(isset($balance['error_code'])){
          error_log("Plaid Error Message: " . $balance_json);
        }            
        //close session
        curl_close($ch);
?>
