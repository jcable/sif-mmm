<?
if (!empty($_REQUEST["text"]))
{

	header("location: manageredundancy.php");
	require 'connect.php';
	$text=$_REQUEST["text"];
	$type=$_REQUEST["type"];
	mysql_query("insert into redundancy (redundancy_text,redundancy_type) values ('$text','$type')", $connection);
}
else
{
echo "Error - Missing pairing name - please go back and check the data.";
}
?>