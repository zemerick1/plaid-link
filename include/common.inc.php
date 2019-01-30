<?php
function getAccounts($dbh, $accountID) {
	if ($accountID == 'All') {
		$sth = $dbh->prepare("SELECT * FROM customers");
		$sth->execute();
		$accounts = $sth->fetchAll(PDO::FETCH_ASSOC);
		unset($sth);
		return $accounts;
	} else {
		$sth = $dbh->prepare("SELECT * FROM customers WHERE account_id = :accountID");
		$sth->bindParam(':accountID', $accountID);
		$sth->execute();
		$accounts = $sth->fetchAll(PDO::FETCH_ASSOC);
		unset($sth);
		return $accounts[0];
	}
}
function plaid_getCreds($dbh, $env) {
	$sth = $dbh->prepare("SELECT * FROM plaid_creds WHERE plaid_env = :env");
	$sth->bindParam(':env', $env);
	$sth->execute();
	$creds = $sth->fetchAll(PDO::FETCH_ASSOC);
	unset($sth);
	return $creds[0];
}
function plaid_getAccounts($client_id, $secret, $token) {
 $data = array(
            "client_id" => $client_id,
            "secret" => $secret,
            "access_token"=>$token
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
	return $acct;

}
function get_plaid_token($dbh, $public_token) {
		$plaid_creds = plaid_getCreds($dbh, 'development');
		
        $data = array(
            "client_id" => $plaid_creds['client_id'],
            "secret" => $plaid_creds['secret'],
            "public_token" => $public_token
        );

        $data_fields = json_encode($data);        

        //initialize session
        $ch=curl_init($plaid_creds['url'] . "/item/public_token/exchange");

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
        $token_json = curl_exec($ch);
        $exchange_token = json_decode($token_json,true);          
        //close session
        curl_close($ch);  
        return $exchange_token;
}
function plaid_getInstitute() { // Not implemented yet.
	
}
function plaid_addAccount($dbh, $cust_data) {
	$sth = $dbh->prepare("INSERT INTO customers (a_token, account_id, item_id, ins_id, ins_name) value (?,?,?,?,?)");
	$sth->execute(array_values($cust_data));
	unset($sth);
	return true;
}
function plaid_getTransactions($dbh, $accountID, $startDate, $endDate) {
	$accounts = getAccounts($dbh, $accountID);
	$plaid_creds = plaid_getCreds($dbh, 'development');
	$data = array(
		"client_id" => $plaid_creds['client_id'],
		"secret" => $plaid_creds['secret'],
		"access_token"=> $accounts['a_token'],
		"start_date" => $startDate,
		"end_date" => $endDate
		);

	$data_fields = json_encode($data);

	//initialize session
	$ch=curl_init($plaid_creds['url'] . "/transactions/get");

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
	
	//check for errors
	if(isset($balance['error_code'])){
		error_log("Plaid Error Message: " . $balance_json);
	}            
	//close session
	curl_close($ch);
	return $balance['transactions'];
}

?>