<?
if (!empty($_REQUEST["originalservice"]))
{
	header("location: manageservices.php");
	require 'connect.php';
	$originalservice=$_REQUEST["originalservice"];
	$service=$_REQUEST["service"];
	$servicelongname=$_REQUEST["servicelongname"];
	$enabled=intval($_REQUEST["enabled"]);
	$locked=intval($_REQUEST["locked"]);
	$icon=$_REQUEST["icon"];
	$tabindex=$_REQUEST["tabindex"];
	$owner=$_REQUEST["owner"];
	$notes=$_REQUEST["notes"];
	$pharosindex=$_REQUEST["pharosindex"];
	mysql_query("update service set service_long_name='$servicelongname' where service='$originalservice'", $connection);
	mysql_query("update service set enabled='$enabled' where service='$originalservice'", $connection);
	mysql_query("update service set locked='$locked' where service='$originalservice'", $connection);
	mysql_query("update service set icon='$icon' where service='$originalservice'", $connection);
	mysql_query("update service set tab_index='$tabindex' where service='$originalservice'", $connection);
	mysql_query("update service set owner='$owner' where service='$originalservice'", $connection);
	mysql_query("update service set notes='$notes' where service='$originalservice'", $connection);
	mysql_query("update service set pharos_index='$pharosindex' where service='$originalservice'", $connection);
	mysql_query("update service set service='$service' where service='$originalservice'", $connection);
}
else
{
echo "Error - no service defined";
}
?>