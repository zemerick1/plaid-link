<?php
  include 'include/config.php';
  include 'include/common.inc.php';
  
?>
<html lang="en">  
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>PHP / Plaid-Link</title>
  <meta name="description" content="">
  <meta name="author" content="">
 
  <script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
  <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
 
  <script type="text/javascript">
 
$(document).ready(function() { 
  // Trigger the standard Institution Select view
  document.getElementById('linkButton').onclick = function() {
    linkHandler.open();
  };  
});        
 
  </script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
 
</head>  
<body>  
<div class="container">
	<!-- <div class="row">
		<div class="col align-self-start">
			LEFT
		</div>
	</div>-->
	<div class="row">
		<div class="col align-self-center">
			<blockquote class="blockquote text-center"><h3>Current Linked Accounts</h3></blockquote>
			<table class="table table-striped">
				<thead class="thead-dark">
					<tr>
						<th scope="col">Name</th>
						<th scope="col">Amount (Current / Available)</th>
						<th scope="col">Type</th>
					</tr>
				</thead>		
				<tbody>
<?php
	$plaid_creds = plaid_getCreds($dbh, 'development');
	$accounts = getAccounts($dbh, 'All'); 
	print_accounts($accounts, $plaid_creds['client_id'], $plaid_creds['secret']);
	
?>
</tbody>
</table>

		</div>
	</div>
	<div class="row">
		<div class="col align-self-center">
			<blockquote class="blockquote text-center"><button id="linkButton" type="button" class="btn btn-primary">Link Your Bank Account</button></blockquote>
		</div>
	</div>
</div>
  <script>
  var linkHandler = Plaid.create({
    selectAccount: true,
    env: '<?php echo $plaid_creds['plaid_env']; ?>',
    apiVersion: 'v2',
    clientName: 'EmerickCC',
    key: '<?php echo $plaid_creds['public']; ?>',
    product: ['auth', 'transactions'],
    //webhook: 'https://myurl.com/webhooks/p_responses.php',
    onLoad: function() {
      // The Link module finished loading.
    },
    onSuccess: function(public_token, metadata) {
		// The onSuccess function is called when the user has successfully
		// authenticated and selected an account to use.     
		$.post('process_plaid_token.php', {
			pt:public_token,
			md:metadata,
			id:"<?php echo '1'; ?>"
			}, function( data ) {                        
				console.log("data : "+data);
				if (data=="Success"){              
					console.log("Success");
					window.location.replace("thankyou.php");//Let users know the process was successful 
				}
				else {
					console.log("Error");
					window.location.replace("error.php");//Let users know the process failed
				}
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
 
<!--<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>  
</html> 
<?php
function print_accounts($accounts, $plaid_client_id, $plaid_secret) {
foreach ($accounts as $k=>$v) {
	$acct = plaid_getAccounts($plaid_client_id, $plaid_secret, $v['a_token']);
        foreach ($acct['accounts'] as $key => $value) {
                $amt_current = sprintf("%0.2f", $value['balances']['current']);
                $amt_avail = sprintf("%0.2f", $value['balances']['available']);
		if (is_null($amt_avail)) { $amt_avail = 'NA'; }

		if (strpos($amt_current, '-') == false) {
			$color = 'text-success';
		}
		else { $color = 'text-danger'; }
		echo '<tr>';
		// shorten ins_name
		$ins_name = substr($v['ins_name'], 0, 15);
		$linkBuild = "<a href=\"transactions.php?acct={$v['account_id']}\">$ins_name: {$value['name']}</a>";
		echo "<td>$linkBuild</td>";
                echo "<td><p class=\"$color\">$amt_current / $amt_avail</p></td>";
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
