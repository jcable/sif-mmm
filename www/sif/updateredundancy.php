<?
if (!empty($_REQUEST["text"]))
{
	header("location: manageredundancy.php");
	require 'connect.php';
	$text=$_REQUEST["text"];
	$main=$_REQUEST["main"];
	$reserve=$_REQUEST["reserve"];
	$tabindex=$_REQUEST["tabindex"];
	mysql_query("update redundancy set main='$main' where redundancy_text='$text'", $connection);
	mysql_query("update redundancy set reserve='$reserve' where redundancy_text='$text'", $connection);
	mysql_query("update redundancy set tab_index='$tabindex' where redundancy_text='$text'", $connection);
}
else
{
echo "Error - no redundancy pair defined";
}
?>