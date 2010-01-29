<?php
if (!empty($_REQUEST["id"]))
{
	header("location: manageredundancy.php");
	require 'sif.inc';
	$dbh = connect();
	$text=$_REQUEST["text"];
	if($_REQUEST["type"] == "SOURCE")
		$sql = "INSERT INTO source2device (id,device) VALUES(?,?)";
	else
		$sql = "INSERT INTO listener2device (id,device) VALUES(?,?)";
	$stmt=$dbh->prepare($sql);
	$stmt->execute(array($_REQUEST["id"], $_REQUEST["device"]));
}
else
{
	echo "Error - Missing pairing name - please go back and check the data.";
}
?>
