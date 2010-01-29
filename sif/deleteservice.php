<?
if (!empty($_REQUEST["service"]))
{
	header("location: manageservices.php");
	require 'connect.php';
	$service=$_REQUEST["service"];
	mysql_query("delete from service where service='$service'", $connection);
}
else
{
echo "Error - Missing service - please go back and check the data.";
}
?>