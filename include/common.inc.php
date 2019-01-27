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
?>
