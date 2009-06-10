<?
if (!empty($_REQUEST["type"]))
{

	header("location: managetabs.php?type=".$_REQUEST["type"]);
	require 'connect.php';
	$type=$_REQUEST["type"];
	$tabtext=$_REQUEST["tabtext"];
	$enabled=intval($_REQUEST["enabled"]);

	switch ($type)	{
			case "source":
				mysql_query("insert into source_tabs (tab_text,enabled) values ('$tabtext','$enabled')", $connection);
				break;
			case "services":
				mysql_query("insert into services_tabs (tab_text,enabled) values ('$tabtext','$enabled')", $connection);
				break;
			case "listener":
				mysql_query("insert into listener_tabs (tab_text,enabled) values ('$tabtext','$enabled')", $connection);
				break;
			case "redundancy":
				mysql_query("insert into redundancy_tabs (tab_text,enabled) values ('$tabtext','$enabled')", $connection);
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