<?
if (!empty($_REQUEST["text"]))
{
	header("location: manageredundancy.php");
	require 'connect.php';
	$text=$_REQUEST["text"];
	mysql_query("delete from redundancy where redundancy_text='$text'", $connection);
}
else
{
echo "Error - Missing redundancy name - please go back and check the data.";
}
?>