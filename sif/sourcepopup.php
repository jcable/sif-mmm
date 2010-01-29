<html>
<head>
<?php
require_once("sif.inc");
$source=$_REQUEST["source"];

print "\n<title>$source</title>";
?>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">
<body>




<?php
$dbh = connect();
//$events = active_schedule_records($dbh,"%");
$events = active_events_as_run($dbh);
//print_r($events);
$numRows =  0;
$services = array();
foreach($events as $event)
{
	if (($source==$event["input"]) || ($source=="OFF"))
	{
		$numRows++;
		$services[] = $event["output"];
	}
}
if($numRows==0)
{
	print "\nSource '{$source}' is currently not routed";
}
else
{
	print "\nSource '{$source}' is currently routed to:";
	print "\n<ul>";
	foreach($services as $service)
	{
		print "\n<li>$service";
	}
	print "\n</ul>";
}
?>
<p>
<form method="post">
<input type="button" value="Close"
onclick="window.close()">
</form>
