<?
if (!empty($_REQUEST["listener"]))
{
	header("location: listenercrashswitch.php?servicetab=".$_REQUEST["servicetab"]."&listenertab=".$_REQUEST["listenertab"]);
	require 'connect.php';
	$service=$_REQUEST["service"];
	$listener=$_REQUEST["listener"];
	mysql_query("update listener set current_service='$service' where id='$listener'", $connection);
}
else
{
header("location: listenercrashswitch.php");
echo "Error - no listener defined";
}
?>
