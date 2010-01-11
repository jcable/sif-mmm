<?php
require_once("messaging.inc");
require_once "sif.inc";
$dbh = connect();
$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
if (isset($_REQUEST["service"]))
{
	if(isset($_REQUEST["sourcetab"]) && isset($_REQUEST["servicetab"]))
	{
		header("location: servicecrashswitch.php?sourcetab=".$_REQUEST["sourcetab"]."&servicetab=".$_REQUEST["servicetab"]);
	}

/*
take the current event and divide it into 4:
	1) update the current event, changing its lastdate to yesterday
	2) insert a new event like the current event, starting tomorrow
	3) insert a new event like the current event, for today only, ending now
	4) insert a new event for the crashed to source, starting now, for today only ending when the current event ends
*/
	$service = $_REQUEST["service"];
	//$previous_source = $_REQUEST["previous_source"];
	$new_source = $_REQUEST["source"];
	//$sed = $_REQUEST["sed"];
	
	//$times = gettimes($dbh);
	//break_schedule($dbh, $service, $new_source, $sed, $times);
	
	$stmt = $dbh->prepare("SELECT value FROM configuration WHERE `key`='message_bus_host'");	
	$stmt->execute();
	$config = $stmt->fetch(PDO::FETCH_ASSOC);
	$sender = new Sender($config["value"]);
	$sender->send($service, "oi");
	//$sender->send($previous_source, "oi");
	$sender->send($source, "oi");
	$sender->close();

	//register_event_as_run($dbh, "ANY", $prev_source, $service, "OFF");
	//register_event_as_run($dbh, "ANY", $new_source, $service, "ON");
}
else
{
	header("location: servicecrashswitch.php");
	echo "Error - no service defined";
}
?>
