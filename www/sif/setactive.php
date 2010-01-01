<?php
if (isset($_REQUEST["id"]) && isset($_REQUEST["idx"]))
{
	header("location: sourcepairs.php?tab=".$_REQUEST["tab"]);
	require 'connect.php';
	$id=$_REQUEST["id"];
	$idx=$_REQUEST["idx"];
	mysql_query("update redundancy set active=0 where id='$id' and idx!=$idx", $connection);
	mysql_query("update redundancy set active=1 where id='$id' and idx=$idx", $connection);
}
else
{
	header("location: sourcepairs.php?tab=1");
	echo "Error - no pair defined";
}
?>
