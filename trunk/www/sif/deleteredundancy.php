<?
if (isset($_REQUEST["id"]) && isset($_REQUEST["idx"]))
{
	header("location: manageredundancy.php");
	require 'connect.php';
	$id=$_REQUEST["id"];
	$idx=$_REQUEST["idx"];
	mysql_query("delete from redundancy where id='$id' and idx=$idx", $connection);
}
else
{
echo "Error - Missing redundancy name - please go back and check the data.";
}
?>
