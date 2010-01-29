<?
if (!empty($_REQUEST["eventid"]))
{
	header("location: showlistenerschedule.php");
	require 'connect.php';
	$eventid=$_REQUEST["eventid"];
	mysql_query("delete from listener_active_schedule where listener_event_id='$eventid'", $connection);
}
else
{
echo "Error - Missing event id - please go back and check the data.";
}
?>