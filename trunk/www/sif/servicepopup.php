<html>
<head>
<?php
require 'sif.inc';
$dbh = connect();
$service=$_REQUEST["service"];

print "\n<title>{$service}</title>";
?>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">
<body>




<?php
$events = active_events_as_run($dbh);
$source = "OFF";
foreach($events as $event)
{
	if($event["service"]==$service)
		$source = $event["source"];
}
print "\nThe source for '{$service}' is '{$source}'";
print "\n<p>";
$stmt=$dbh->prepare("SELECT * FROM listener where current_service=? order by id asc");
$stmt->bindValue(1, $service);
$sched = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(count($sched)==0)
{
	print "\nService '{$service}' is currently not routed";
}
else
{
	print "\nService '{$service}' is currently routed to:";
	print "\n<ul>";
	foreach($sched as $row)
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
</body>
</html>