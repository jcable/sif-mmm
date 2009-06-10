<?
if (!empty($_REQUEST["originalsource"]))
{
	header("location: managesources.php");
	require 'connect.php';
	$originalsource=$_REQUEST["originalsource"];
	$source=$_REQUEST["source"];
	$sourcelongname=$_REQUEST["sourcelongname"];
	$enabled=intval($_REQUEST["enabled"]);
	$active=intval($_REQUEST["active"]);
	$role=$_REQUEST["role"];
	$pharosindex=$_REQUEST["pharosindex"];
	$vlchostname=$_REQUEST["vclhostname"];
	$icon=$_REQUEST["icon"];
	$tabindex=$_REQUEST["tabindex"];
	$owner=$_REQUEST["owner"];
	$notes=$_REQUEST["notes"];

	mysql_query("update source set source_long_name='$sourcelongname' where source='$originalsource'", $connection);
	mysql_query("update source set active='$active' where source='$originalsource'", $connection);
	mysql_query("update source set role='$role' where source='$originalsource'", $connection);
	mysql_query("update source set enabled='$enabled' where source='$originalsource'", $connection);
	mysql_query("update source set vlc_hostname='$vlchostname' where source='$originalsource'", $connection);
	mysql_query("update source set icon='$icon' where source='$originalsource'", $connection);
	mysql_query("update source set tab_index='$tabindex' where source='$originalsource'", $connection);
	mysql_query("update source set owner='$owner' where source='$originalsource'", $connection);
	mysql_query("update source set notes='$notes' where source='$originalsource'", $connection);
	mysql_query("update source set pharos_index='$pharosindex' where source='$originalsource'", $connection);
	mysql_query("update source set source='$source' where source='$originalsource'", $connection);
}
else
{
echo "Error - no source defined";
}
?>