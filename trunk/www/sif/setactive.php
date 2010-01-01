<?php
if (!empty($_REQUEST["pair"]))
{
	header("location: sourcepairs.php?pairtab=".$_REQUEST["pairtab"]);
	require 'connect.php';
	$id=$_REQUEST["pair"];
	$device=$_REQUEST["device"];
	mysql_query("update redundancy set active=0 where id='$id' and device!='device'", $connection);
	mysql_query("update redundancy set active=1 where id='$id' and device='device'", $connection);
}
else
{
	header("location: pairtab.php?pairtab=1");
	echo "Error - no pair defined";
}
?>
