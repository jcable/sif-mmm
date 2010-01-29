<?
if (!empty($_REQUEST["matid"]))
{
	header("location: showmaterial.php");
	require 'connect.php';
	$matid=$_REQUEST["matid"];
	mysql_query("delete from material where material_id='$matid'", $connection);
}
else
{
echo "Error - Missing material - please go back and check the data.";
}
?>