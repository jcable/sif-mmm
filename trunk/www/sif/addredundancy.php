<?
if (!empty($_REQUEST["text"]))
{
	header("location: manageredundancy.php");
	require 'connect.php';
	$text=$_REQUEST["text"];
	if($_REQUEST["type"] == "SOURCE")
		mysql_query("insert into source2device (id) values ('$text')", $connection);
	else
		mysql_query("insert into listener2device (id) values ('$text')", $connection);
}
else
{
echo "Error - Missing pairing name - please go back and check the data.";
}
?>
