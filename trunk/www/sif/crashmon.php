<?
if (!empty($_REQUEST["mon"]))
{
	header("location: monitor.php?servicetab=".$_REQUEST["servicetab"]."&sourcetab=".$_REQUEST["sourcetab"]);
	require 'connect.php';
	$source=$_REQUEST["monsource"];
	$mon=$_REQUEST["mon"];
	mysql_query("update listener set current_service='$source' where listener='$mon'", $connection);

}
else
{
header("location: monitor.php?servicetab=1&listenertab=1");
echo "Error - no monitor destination defined";
}
?>