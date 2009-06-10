<?
if (!empty($_REQUEST["listener"]))
{

	header("location: managelisteners.php");
	require 'connect.php';
	$listener=$_REQUEST["listener"];
	$enabled=intval($_REQUEST["enabled"]);
	mysql_query("insert into listener (listener,enabled) values ('$listener','$enabled')", $connection);
}
else
{
echo "Error - Missing listener name - please go back and check the data.";
}
?>