<?
if (!empty($_REQUEST["source"]))
{

	header("location: managesources.php");
	require 'connect.php';
	$source=$_REQUEST["source"];
	$enabled=intval($_REQUEST["enabled"]);
	mysql_query("insert into source (source,enabled) values ('$source','$enabled')", $connection);
}
else
{
echo "Error - Missing source name - please go back and check the data.";
}
?>