<html>
<head>
<?php
require 'connect.php';
$service=$_REQUEST["service"];

print "\n<title>{$service}</title>";
?>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">
<body>




<?
$result=mysql_query("SELECT * FROM service where service='$service' order by service asc", $connection);
while($row= mysql_fetch_array($result))
{
	$source=$row[current_source];
	if ($source=="")
	{
		$source="OFF";
	}
	print "\nThe source for '{$service}' is '{$source}'";
}
print "\n<p>";
$result=mysql_query("SELECT * FROM listener where current_service='$service' order by id asc", $connection);
$numRows = mysql_num_rows($result);
if($numRows==0)
{
	print "\nService '{$service}' is currently not routed";
}
else
{
	print "\nService '{$service}' is currently routed to:";
	print "\n<ul>";
	while($row=mysql_fetch_array($result))
	{
		print "\n<li>{".$row["id"]."}";
	}
	print "\n</ul>";
}
?>
<p>
<form method="post">
<input height=50 type="button" value="Close"
onclick="window.close()">
</form>
