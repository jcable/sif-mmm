<?
if (!empty($_REQUEST["listener"]))
{
	header("location: managelisteners.php");
	require 'connect.php';
	$listener=$_REQUEST["listener"];
	mysql_query("delete from listener where listener='$listener'", $connection);
}
else
{
echo "Error - Missing listener - please go back and check the data.";
}
?>