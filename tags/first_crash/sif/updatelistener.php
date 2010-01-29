<?
if (!empty($_REQUEST["originallistener"]))
{
	header("location: managelisteners.php");
	require 'connect.php';
	$originallistener=$_REQUEST["originallistener"];
	$listener=$_REQUEST["listener"];
	$listenerlongname=$_REQUEST["listenerlongname"];
	$enabled=intval($_REQUEST["enabled"]);
	$defaultservice=$_REQUEST["defaultservice"];
	$autoservice=intval($_REQUEST["autoservice"]);
	$role=$_REQUEST["role"];
	$pharosindex=$_REQUEST["pharosindex"];
	$icon=$_REQUEST["icon"];
	$tabindex=$_REQUEST["tabindex"];
	$owner=$_REQUEST["owner"];
	$notes=$_REQUEST["notes"];
	if ($role == "MONITOR")
	{
		$autoservice=1;
		$enabled=0;
		$tabindex=0;
	}
	mysql_query("update listener set listener_long_name='$listenerlongname' where id='$originallistener'", $connection);
	mysql_query("update listener set default_service='$defaultservice' where id='$originallistener'", $connection);
	mysql_query("update listener set auto_service='$autoservice' where id='$originallistener'", $connection);
	mysql_query("update listener set enabled='$enabled' where id='$originallistener'", $connection);
	mysql_query("update listener set role='$role' where id='$originallistener'", $connection);
	mysql_query("update listener set icon='$icon' where id='$originallistener'", $connection);
	mysql_query("update listener set tab_index='$tabindex' where id='$originallistener'", $connection);
	mysql_query("update listener set owner='$owner' where id='$originallistener'", $connection);
	mysql_query("update listener set notes='$notes' where id='$originallistener'", $connection);
	mysql_query("update listener set pharos_index='$pharosindex' where id='$originallistener'", $connection);
	mysql_query("update listener set listener='$listener' where id='$originallistener'", $connection);
	if ($autoservice==1)
	{
		// if it is an autolistener, then the service is set to its own name and an attempt is made to create a
		// listener with the same name, which will not be enabled so it will not show up in panels (the monitor
		// panel picks up destinations from listeners with role=monitor
		mysql_query("update listener set current_service='$listener' where id='$originallistener'", $connection);
		mysql_query("update listener set default_service='$listener' where id='$originallistener'", $connection);
		mysql_query("insert into service (service,enabled) values ('$listener','0')", $connection);
	}
}
else
{
echo "Error - no listener defined";
}
?>
