<?php
function getAllAccounts($dbh) {
	$sth = $dbh->prepare("SELECT * FROM customers");
	$sth->execute();
	$accounts = $sth->fetchAll(PDO::FETCH_ASSOC);
	unset($sth);
	return $accounts;
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
function get_plaid_token($public_token) {
        global $plaid_client_id, $plaid_secret, $plaid_url;
        $data = array(
            "client_id" => $plaid_client_id,
            "secret" => $plaid_secret,
            "public_token" => $public_token
        );

        $data_fields = json_encode($data);        

        //initialize session
        $ch=curl_init($plaid_url . "/item/public_token/exchange");

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
function plaid_getInstiture() { // Not implemented yet.
	
}
function plaid_addAccount($dbh, $cust_data) {
	$sth = $dbh->prepare("INSERT INTO customers (a_token, account_id, item_id, ins_id, ins_name) value (?,?,?,?,?)");
	$sth->execute(array_values($cust_data));
	unset($sth);
	return true;
}
?>
