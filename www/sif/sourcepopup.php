<html>
<head>
<?php
require 'connect.php';
$source=$_REQUEST["source"];

print "\n<title>{$source}</title>";
?>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">
<body>




<?
$result=mysql_query("SELECT * FROM service where current_source='$source' order by service asc", $connection);
$numRows = mysql_num_rows($result);

if ($source=="OFF")
{
	// also count services with no source defined - this is the same as OFF
	$offresult=mysql_query("SELECT * FROM service where current_source='' or current_source is NULL order by service asc", $connection);
	$numRows = $numRows+mysql_num_rows($offresult);
}
if($numRows==0)
{
	print "\nSource '{$source}' is currently not routed";
}
else
{
	print "\nSource '{$source}' is currently routed to:";
	print "\n<ul>";
	while($row= mysql_fetch_array($result))
	{
		print "\n<li>{$row[service]}";
	}
	if ($source=="OFF")
	{
		// also list services with no source defined - this is the same as OFF
		$result=mysql_query("SELECT * FROM service where current_source='' or current_source is NULL order by service asc", $connection);
		while($row= mysql_fetch_array($result))
		{
			print "\n<li>{$row[service]}";
		}
	}
	print "\n</ul>";
}
?>
<p>
<form method="post">
<input type="button" value="Close"
onclick="window.close()">
</form>