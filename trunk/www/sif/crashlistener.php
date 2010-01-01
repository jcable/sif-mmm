<?
if (!empty($_REQUEST["listener"]))
{
	header("location: listenercrashswitch.php?servicetab=".$_REQUEST["servicetab"]."&listenertab=".$_REQUEST["listenertab"]);
	require 'connect.php';
	$service=$_REQUEST["service"];
	$listener=$_REQUEST["listener"];
	$hold=$_REQUEST["hold"];
	mysql_query("update listener set current_service='$service' where id='$listener'", $connection);
	mysql_query("update listener set locked=$hold where id='$listener'", $connection);
}
else
{
header("location: listenercrashswitch.php");
echo "Error - no listener defined";
}
?>
