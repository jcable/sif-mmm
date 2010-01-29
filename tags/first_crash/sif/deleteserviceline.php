<?
if (!empty($_REQUEST["eventid"]))
{
	header("location: showserviceschedule.php");
	require 'connect.php';
	$eventid=$_REQUEST["eventid"];
	mysql_query("delete from service_active_schedule where service_event_id='$eventid'", $connection);
}
else
{
echo "Error - Missing event id - please go back and check the data.";
}
?>