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
 
 

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
 
</head>  
<body>
<?php
$plaid_creds = plaid_getCreds($dbh, 'development');
$accounts = getAccounts($dbh, 'All');
?>
<div class="container">
	<!-- <div class="row">
		<div class="col align-self-start">
			LEFT
		</div>
	</div>-->
	<div class="row">
		<div class="col align-self-center">
			<blockquote class="blockquote text-center"><h3>Select Accounts to Move Dat Money</h3></blockquote>
			<form>
			  <div class="form-group">
				<label for="exampleFormControlSelect1">From Account:</label>
				<select class="form-control" id="exampleFormControlSelect1">
					<?php print_accounts($accounts, $plaid_creds); ?>
				</select>
				</div>
				<div class="form-group">
				<label for="exampleFormControlSelect1">To Account:</label>
				<select class="form-control" id="exampleFormControlSelect1">
					<?php print_accounts($accounts, $plaid_creds); ?>
				</select>
				</div>
				  <div class="form-group">
					<label for="amount">Dollar Amount</label>
					<input type="text" class="form-control" id="amount" placeholder="Amount (USD)">
				  </div>
			  <button type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col align-self-center">
			<blockquote class="blockquote text-center">Centered Text (New Row)</blockquote>
		</div>
	</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>  
</html>
<?php
function print_accounts($accounts, $plaid_creds) {
foreach ($accounts as $k=>$v) {
	$acct = plaid_getAccounts($plaid_creds['client_id'], $plaid_creds['secret'], $v['a_token']);
        foreach ($acct['accounts'] as $key => $value) {
			$ins_name = substr($v['ins_name'], 0, 15);
            $amt_current = sprintf("%0.2f", $value['balances']['current']);
            $amt_avail = sprintf("%0.2f", $value['balances']['available']);
			
			if ($value['subtype'] == 'checking' || $value['subtype'] == 'savings') { $canTransfer = True; }
			else { $canTransfer = False; }
			if ($canTransfer) { 
				echo "<option>" . $ins_name . " : " . $value['subtype'] . " : " . $amt_current . " / " . $amt_avail . "</option>";
			}
}

        //check for errors
        if(isset($acct['error_code'])){
          error_log("Plaid Error Message: " . $balance_json);
        }
  }
}
?>