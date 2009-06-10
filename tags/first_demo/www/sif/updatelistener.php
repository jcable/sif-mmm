<?
if (!empty($_REQUEST["originallistener"]))
{
	header("location: managelisteners.php");
	require 'connect.php';
	$originallistener=$_REQUEST["originallistener"];
	$listener=$_REQUEST["listener"];
	$listenerlongname=$_REQUEST["listenerlongname"];
	$enabled=intval($_REQUEST["enabled"]);
	$locked=intval($_REQUEST["locked"]);
	$defaultservice=$_REQUEST["defaultservice"];
	$autoservice=intval($_REQUEST["autoservice"]);
	$role=$_REQUEST["role"];
	$pharosindex=$_REQUEST["pharosindex"];
	$vlchostname=intval($_REQUEST["vclhostname"]);
	$icon=$_REQUEST["icon"];
	$tabindex=$_REQUEST["tabindex"];
	$owner=$_REQUEST["owner"];
	$notes=$_REQUEST["notes"];
	if ($role == "MONITOR")
	{
		$autoservice=1;
	}
	mysql_query("update listener set listener_long_name='$listenerlongname' where listener='$originallistener'", $connection);
	mysql_query("update listener set locked='$locked' where listener='$originallistener'", $connection);
	mysql_query("update listener set default_service='$defaultservice' where listener='$originallistener'", $connection);
	mysql_query("update listener set auto_service='$autoservice' where listener='$originallistener'", $connection);
	mysql_query("update listener set enabled='$enabled' where listener='$originallistener'", $connection);
	mysql_query("update listener set role='$role' where listener='$originallistener'", $connection);
	mysql_query("update listener set vlchostname='$vlchostname' where listener='$originallistener'", $connection);
	mysql_query("update listener set icon='$icon' where listener='$originallistener'", $connection);
	mysql_query("update listener set tab_index='$tabindex' where listener='$originallistener'", $connection);
	mysql_query("update listener set owner='$owner' where listener='$originallistener'", $connection);
	mysql_query("update listener set notes='$notes' where listener='$originallistener'", $connection);
	mysql_query("update listener set pharos_index='$pharosindex' where listener='$originallistener'", $connection);
	mysql_query("update listener set listener='$listener' where listener='$originallistener'", $connection);
}
else
{
echo "Error - no listener defined";
}
?>