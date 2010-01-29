<?
if (!empty($_REQUEST["originalsource"]))
{
	header("location: managesources.php");
	require 'connect.php';
	$originalsource=$_REQUEST["originalsource"];
	$source=$_REQUEST["source"];
	$sourcelongname=$_REQUEST["sourcelongname"];
	$enabled=intval($_REQUEST["enabled"]);
	$role=$_REQUEST["role"];
	$pharosindex=$_REQUEST["pharosindex"];
	$icon=$_REQUEST["icon"];
	$tabindex=$_REQUEST["tabindex"];
	$owner=$_REQUEST["owner"];
	$notes=$_REQUEST["notes"];

	mysql_query("update source set long_name='$sourcelongname' where id='$originalsource'", $connection);
	mysql_query("update source set role='$role' where id='$originalsource'", $connection);
	mysql_query("update source set enabled='$enabled' where id='$originalsource'", $connection);
	mysql_query("update source set icon='$icon' where id='$originalsource'", $connection);
	mysql_query("update source set tab_index='$tabindex' where id='$originalsource'", $connection);
	mysql_query("update source set owner='$owner' where id='$originalsource'", $connection);
	mysql_query("update source set notes='$notes' where id='$originalsource'", $connection);
	mysql_query("update source set pharos_index='$pharosindex' where id='$originalsource'", $connection);
	mysql_query("update source set source='$source' where id='$originalsource'", $connection);
}
else
{
echo "Error - no source defined";
}
?>
