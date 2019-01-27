<?php
function getAllAccounts($dbh) {
	$sth = $dbh->prepare("SELECT * FROM customers");
	$sth->execute();
	$accounts = $sth->fetchAll(PDO::FETCH_ASSOC);
	unset($sth);
	return $accounts;
}
?>
