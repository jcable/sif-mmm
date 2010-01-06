<?
if (isset($_REQUEST["service"]))
{
	header("location: servicecrashswitch.php?sourcetab=".$_REQUEST["sourcetab"]."&servicetab=".$_REQUEST["servicetab"]);
	require 'connect.php';
	$source=$_REQUEST["source"];
	$service=$_REQUEST["service"];
	$hold=$_REQUEST["hold"];
	//mysql_query("update service set current_source='$source' where service='$service'", $connection);
	if ($hold==1)
	{
		mysql_query("update service set locked=1 where service='$service'", $connection);
	}
	else
	{
		mysql_query("update service set locked=0 where service='$service'", $connection);
	}
}
else
{
header("location: servicecrashswitch.php?sourcetab=1&servicetab=1");
echo "Error - no service defined";
}
?>