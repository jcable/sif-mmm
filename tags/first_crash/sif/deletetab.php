<?
if (!empty($_REQUEST["type"]) &&
!empty($_REQUEST["id"]))
{
	header("location: managetabs.php?type=".$_REQUEST["type"]);
	require 'connect.php';
	$type=$_REQUEST["type"];
	$id=$_REQUEST["id"];
	$tabtext=$_REQUEST["tabtext"];
	$enabled=intval($_REQUEST["enabled"]);
	switch ($type)	{
			case "source":
				mysql_query("delete from source_tabs where tab_index='$id'", $connection);
				break;
			case "services":
				mysql_query("delete from services_tabs where tab_index='$id'", $connection);
				break;
			case "listener":
				mysql_query("delete from listener_tabs where tab_index='$id'", $connection);
				break;
			case "redundancy":
				mysql_query("delete from redundancy_tabs where tab_index='$id'", $connection);
				break;
			default:
				echo "Error - bad type defined";
	}
}
else
{
echo "Error - Missing type or id - please go back and check the data.";
}
?>