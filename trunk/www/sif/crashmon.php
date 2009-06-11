<?
if (!empty($_REQUEST["mon"]))
{
	header("location: monitor.php?servicetab=".$_REQUEST["servicetab"]."&sourcetab=".$_REQUEST["sourcetab"]);
	require 'connect.php';
	$source=$_REQUEST["monsource"];
	$mon=$_REQUEST["mon"];
	$smon=$_REQUEST["sourcemon"];
	if ($source=="OFF")
	{
		mysql_query("update service set current_source='OFF' where service='$mon'", $connection);
		mysql_query("update listener set current_service='OFF' where listener='$mon'", $connection);
	}
	else
	{
		if ($smon=="yes")
		{

			mysql_query("update service set current_source='$source' where service='$mon'", $connection);
			mysql_query("update listener set current_service='$mon' where listener='$mon'", $connection);
		}
		else
		{
			mysql_query("update service set current_source='OFF' where service='$mon'", $connection);
			mysql_query("update listener set current_service='$source' where listener='$mon'", $connection);
		}
	}
}
else
{
header("location: monitor.php?servicetab=1&listenertab=1");
echo "Error - no monitor destination defined";
}
?>