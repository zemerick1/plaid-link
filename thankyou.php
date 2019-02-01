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
<div class="container">
	<!-- <div class="row">
		<div class="col align-self-start">
			LEFT
		</div>
	</div>-->
	<div class="row">
		<div class="col align-self-center">
			<blockquote class="blockquote text-center"><h3>Account Successfully Linked</h3></blockquote>
			<blockquote class="blockquote text-center"><button type="button" class="btn btn-primary" onClick="location.href='index.php';">View Accounts</button></blockquote>
<?php
	$plaid_creds = plaid_getCreds($dbh, 'development');
	$accounts = getAccounts($dbh, 'All'); 
	print_accounts($accounts, $plaid_creds['client_id'], $plaid_creds['secret']);
	
?>

		</div>
	</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>  
</html> 