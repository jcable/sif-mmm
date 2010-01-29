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
				mysql_query("update source_tabs set tab_text='$tabtext' where tab_index='$id'", $connection);
				mysql_query("update source_tabs set enabled='$enabled' where tab_index='$id'", $connection);
				break;
			case "services":
				mysql_query("update services_tabs set tab_text='$tabtext' where tab_index='$id'", $connection);
				mysql_query("update services_tabs set enabled='$enabled' where tab_index='$id'", $connection);
				break;
			case "listener":
				mysql_query("update listener_tabs set tab_text='$tabtext' where tab_index='$id'", $connection);
				mysql_query("update listener_tabs set enabled='$enabled' where tab_index='$id'", $connection);
				break;
			case "redundancy":
				mysql_query("update redundancy_tabs set tab_text='$tabtext' where tab_index='$id'", $connection);
				mysql_query("update redundancy_tabs set enabled='$enabled' where tab_index='$id'", $connection);
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