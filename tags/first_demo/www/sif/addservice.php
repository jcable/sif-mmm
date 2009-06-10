<?
if (!empty($_REQUEST["service"]))
{

	header("location: manageservices.php");
	require 'connect.php';
	$service=$_REQUEST["service"];
	$enabled=intval($_REQUEST["enabled"]);
	mysql_query("insert into service (service,enabled) values ('$service','$enabled')", $connection);
}
else
{
echo "Error - Missing service name - please go back and check the data.";
}
?>