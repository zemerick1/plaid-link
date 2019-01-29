<?php
	include 'include/config.php';
	include 'include/common.inc.php';
?>
<!doctype html>
<META HTTPEQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<meta httpequiv="expires" content="0" />
<html lang="en">
<head>
  <title>Transactions</title>
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
			<blockquote class="blockquote text-center"><h3>Transactions for last 30 Days</h3></blockquote>
			<table class="table table-striped">
				<thead class="thead-dark">
					<tr>
						<th scope="col">Place</th>
						<th scope="col">Amount</th>
						<th scope="col">Date</th>
					</tr>
				</thead>		
				<tbody>
<?php 
	// Get Transactions
	$account_id = $_GET['acct'];
	$date = date('Y-m-d');
	$startDate = date('Y-m-d', strtotime($date. ' - 30 days'));
	$transactions = plaid_getTransactions($dbh, $account_id ,$startDate, date('Y-m-d'));
	foreach($transactions as $key => $value) {
		echo "<tr>";
		echo "<td>{$value['name']}</td>";
		echo "<td>{$value['amount']}</td>";
		echo "<td>{$value['date']}</td>";
		echo "</td>";
	}
?>
</tbody>
</table>

		</div>
	</div>
	<div class="row">
		<div class="col align-self-center">
			<blockquote class="blockquote text-center">Centered Text</blockquote>
		</div>
	</div>
</div>
<?php

?>
<!--<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>
</html>