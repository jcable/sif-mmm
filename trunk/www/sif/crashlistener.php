<?
if (!empty($_REQUEST["listener"]))
{
	header("location: listenercrashswitch.php?servicetab=".$_REQUEST["servicetab"]."&listenertab=".$_REQUEST["listenertab"]);
	require 'connect.php';
	$service=$_REQUEST["service"];
	$listener=$_REQUEST["listener"];
	$hold=$_REQUEST["hold"];
	mysql_query("update listener set current_service='$service' where listener='$listener'", $connection);
	if ($hold==1)
	{
		mysql_query("update listener set locked=1 where listener='$listener'", $connection);
	}
	else
	{
		mysql_query("update listener set locked=0 where listener='$listener'", $connection);
	}
	// now do any redundant pairs
	$result=mysql_query("SELECT * FROM redundancy where main='$listener'", $connection);
		while($row= mysql_fetch_array($result))
		{
			$reserve=$row["reserve"];
			mysql_query("update listener set current_service='$service' where listener='$reserve'", $connection);
			if ($hold==1)
				{
					mysql_query("update listener set locked=1 where listener='$reserve'", $connection);
				}
				else
				{
					mysql_query("update listener set locked=0 where listener='$reserve'", $connection);
				}


		}
}
else
{
header("location: listenercrashswitch.php?servicetab=1&listenertab=1");
echo "Error - no listener defined";
}
?>