<?
if (!empty($_REQUEST["source"]))
{
	header("location: managesources.php");
	require 'connect.php';
	$source=$_REQUEST["source"];
	mysql_query("delete from source where source='$source'", $connection);
}
else
{
echo "Error - Missing source - please go back and check the data.";
}
?>